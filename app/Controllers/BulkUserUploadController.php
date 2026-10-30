<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/View.php';
require_once dirname(__DIR__).'/Models/UploadJob.php';

final class BulkUserUploadController
{
    private const HEADERS = ['serial_no','name','church','hq_dept','ph_dept','region','area','birthday','contact_number','email'];

    public function template(): void
    {
        require_super_admin();
        $file = tempnam(sys_get_temp_dir(), 'lh-users-');
        $zip = new ZipArchive();
        $zip->open($file, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Users" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $cells = '';
        foreach (self::HEADERS as $i => $header) {
            $col = self::columnName($i + 1);
            $cells .= '<c r="'.$col.'1" t="inlineStr"><is><t>'.$header.'</t></is></c>';
        }
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1">'.$cells.'</row></sheetData></worksheet>');
        $zip->close();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="lighthouse-user-template.xlsx"');
        header('Content-Length: '.filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }

    public function upload(): void
    {
        $user = require_super_admin();
        csrf();
        try {
            $file = $_FILES['users_file'] ?? null;
            if (!$file || $file['error'] !== UPLOAD_ERR_OK || strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'xlsx') {
                throw new RuntimeException('Upload a valid .xlsx file.');
            }
            $rows = $this->readRows($file['tmp_name']);
            $jobId=(int)($_POST['upload_job_id']??0);
            $job=db()->prepare("SELECT id,status FROM upload_jobs WHERE id=? AND user_id=? AND upload_type='user_bulk'"); $job->execute([$jobId,$user['id']]); $jobState=$job->fetch();
            if(!$jobState||!in_array($jobState['status'],['preparing','uploading'],true))throw new RuntimeException('The upload session expired. Start the upload again.');
            db()->beginTransaction();
            try {
                db()->prepare("UPDATE upload_jobs SET original_filename=?,file_size=?,status='processing',total_count=?,processed_count=0,created_count=0,updated_count=0,progress=0 WHERE id=? AND user_id=?")->execute([basename((string)$file['name']),(int)$file['size'],max(0,count($rows)-1),$jobId,$user['id']]);
                $insert = db()->prepare('INSERT INTO bulk_user_upload_rows (job_id,excel_row,row_data,serial_no) VALUES (?,?,?,?)');
                foreach ($rows as $excelRow => $row) {
                    if ($excelRow===1) continue;
                    $data=[];
                    foreach(self::HEADERS as $column=>$field)$data[$field]=trim((string)($row[$column]??''));
                    $insert->execute([$jobId,$excelRow,json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$data['serial_no']?:null]);
                }
                db()->commit();
            } catch(Throwable $e) { if(db()->inTransaction())db()->rollBack(); throw $e; }
            $this->json(['id'=>$jobId,'status'=>'processing','total'=>max(0,count($rows)-1)]);
        } catch (Throwable $e) {
            $this->json(['error'=>$e->getMessage()],422);
        }
    }

    public function process(array $params): void
    {
        $user=require_super_admin(); csrf();
        $jobId=(int)$params['id'];
        $job=db()->prepare("SELECT id,total_count,processed_count,created_count,updated_count,status,progress FROM upload_jobs WHERE id=? AND user_id=? AND upload_type='user_bulk'");
        $job->execute([$jobId,$user['id']]); $state=$job->fetch();
        if(!$state){$this->json(['error'=>'Upload job not found.'],404);return;}
        if($state['status']==='completed'||$state['status']==='failed'){$this->json($this->progressPayload($state));return;}

        db()->beginTransaction();
        try {
            $pending=db()->prepare("SELECT id,excel_row,row_data FROM bulk_user_upload_rows WHERE job_id=? AND status='pending' ORDER BY excel_row LIMIT 50 FOR UPDATE");
            $pending->execute([$jobId]); $batch=$pending->fetchAll();
            foreach($batch as $item){
            $data=json_decode($item['row_data'],true)?:[];
            $result=$this->applyRow($data);
            if($result['status']==='failed'){
                db()->prepare("UPDATE bulk_user_upload_rows SET status='failed',serial_no=?,error_message=? WHERE id=?")->execute([$data['serial_no']?:null,$result['reason'],$item['id']]);
            }else{
                db()->prepare('UPDATE bulk_user_upload_rows SET status=?,serial_no=? WHERE id=?')->execute([$result['status'],$data['serial_no']?:null,$item['id']]);
            }
            db()->prepare('UPDATE upload_jobs SET processed_count=processed_count+1,created_count=created_count+?,updated_count=updated_count+?,progress=IF(total_count=0,100,LEAST(99,FLOOR((processed_count+1)*100/total_count))) WHERE id=?')->execute([$result['status']==='created'?1:0,$result['status']==='updated'?1:0,$jobId]);
            }
            $remaining=db()->prepare("SELECT COUNT(*) FROM bulk_user_upload_rows WHERE job_id=? AND status='pending'"); $remaining->execute([$jobId]);
            if((int)$remaining->fetchColumn()===0)UploadJob::complete($jobId,(int)$user['id'],'user_bulk',$jobId);
            db()->commit();
        } catch(Throwable $e) {
            if(db()->inTransaction())db()->rollBack();
            $this->json(['error'=>'Could not process this batch. Please retry.'],500); return;
        }
        $job->execute([$jobId,$user['id']]); $state=$job->fetch();
        $this->json($this->progressPayload($state));
    }

    public function results(array $params): void
    {
        $user=require_super_admin(); $jobId=(int)$params['id'];
        $job=db()->prepare("SELECT id,status,total_count,processed_count,created_count,updated_count,error_message FROM upload_jobs WHERE id=? AND user_id=? AND upload_type='user_bulk'");
        $job->execute([$jobId,$user['id']]); $state=$job->fetch();
        if(!$state){http_response_code(404);render('Not found','<p>Upload job not found.</p>');return;}
        $failed=db()->prepare("SELECT excel_row,serial_no,error_message AS reason FROM bulk_user_upload_rows WHERE job_id=? AND status='failed' ORDER BY excel_row"); $failed->execute([$jobId]);
        view('profile/user-upload-results',['title'=>'Bulk Upload Results','job'=>$state,'created'=>(int)$state['created_count'],'updated'=>(int)$state['updated_count'],'errors'=>$failed->fetchAll()]);
    }

    private function applyRow(array $data): array
    {
        $sn=(string)($data['serial_no']??''); $name=(string)($data['name']??'');
        if($sn==='')return ['status'=>'failed','reason'=>'Missing required serial_no'];
        if($name==='')return ['status'=>'failed','reason'=>'Missing required name'];
        if(mb_strlen($sn)>100)return ['status'=>'failed','reason'=>'serial_no exceeds 100 characters'];
        if(mb_strlen($name)>120)return ['status'=>'failed','reason'=>'name exceeds 120 characters'];
        if(mb_strlen((string)($data['email']??''))>255)return ['status'=>'failed','reason'=>'Email exceeds 255 characters'];
        $birthday=null;
        if(($data['birthday']??'')!==''){
            $birthday=$this->birthday((string)$data['birthday']);
        }
        try{
            $existing=db()->prepare('SELECT id,email FROM users WHERE serial_no=?'); $existing->execute([$sn]); $account=$existing->fetch();
            if($account)return $this->updateExistingBySerial($account,$data,$birthday);

            db()->prepare('INSERT INTO users (serial_no,name,email,church,hq_dept,ph_dept,region,area,birthday,contact_number,password,role,must_change_password) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1)')->execute([$sn,$name,$data['email']?:null,$data['church']?:null,$data['hq_dept']?:null,$data['ph_dept']?:null,$data['region']?:null,$data['area']?:null,$birthday,$data['contact_number']?:null,password_hash('lighthouse@mop',PASSWORD_ARGON2ID),'admin']);
            return ['status'=>'created'];
        }catch(Throwable $e){
            if($e instanceof PDOException&&$e->getCode()==='23000'){
                $existing=db()->prepare('SELECT id,email FROM users WHERE serial_no=?');$existing->execute([$sn]);$account=$existing->fetch();
                if($account){
                    try{return $this->updateExistingBySerial($account,$data,$birthday);}
                    catch(Throwable $updateError){return ['status'=>'failed','reason'=>'Could not update the existing serial_no: '.$this->databaseReason($updateError)];}
                }
                return ['status'=>'failed','reason'=>'serial_no already belongs to another user'];
            }
            return ['status'=>'failed','reason'=>$this->databaseReason($e)];
        }
    }

    private function updateExistingBySerial(array $account,array $data,?string $birthday): array
    {
        $email=(string)($data['email']??'');
        db()->prepare('UPDATE users SET name=?,email=?,church=?,hq_dept=?,ph_dept=?,region=?,area=?,birthday=?,contact_number=? WHERE id=?')->execute([(string)$data['name'],$email?:null,$data['church']?:null,$data['hq_dept']?:null,$data['ph_dept']?:null,$data['region']?:null,$data['area']?:null,$birthday,$data['contact_number']?:null,$account['id']]);
        return ['status'=>'updated'];
    }

    private function databaseReason(Throwable $error): string
    {
        if($error instanceof PDOException){
            $driverCode=(int)($error->errorInfo[1]??0);
            if($driverCode===1062)return 'serial_no already belongs to another user';
            if($driverCode===1406)return 'A value exceeds the database column length';
            if($driverCode===1048)return 'A required database value is missing';
        }
        return 'Could not save row';
    }

    private function progressPayload(array $state): array
    {
        return ['id'=>(int)$state['id'],'status'=>$state['status'],'total'=>(int)$state['total_count'],'processed'=>(int)$state['processed_count'],'created'=>(int)$state['created_count'],'updated'=>(int)$state['updated_count'],'progress'=>(int)($state['progress']??0)];
    }

    private function json(array $data,int $status=200): void
    {
        http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    private function readRows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) throw new RuntimeException('The Excel file is unreadable or invalid.');
        $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($xml === false) { $zip->close(); throw new RuntimeException('The workbook has no first worksheet.'); }
        $shared = [];
        if (($sharedXml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $strings = simplexml_load_string($sharedXml);
            foreach ($strings->si ?? [] as $item) $shared[] = (string)($item->t ?? implode('', array_map('strval', iterator_to_array($item->r ?? []))));
        }
        $sheet = simplexml_load_string($xml);
        $rows = [];
        foreach ($sheet->sheetData->row ?? [] as $rowNode) {
            $row = [];
            foreach ($rowNode->c ?? [] as $cell) {
                preg_match('/^([A-Z]+)/', (string)$cell['r'], $match);
                $index = self::columnIndex($match[1] ?? 'A') - 1;
                $value = (string)($cell->v ?? '');
                if ((string)$cell['t'] === 's') $value = $shared[(int)$value] ?? '';
                elseif ((string)$cell['t'] === 'inlineStr') $value = (string)($cell->is->t ?? '');
                $row[$index] = $value;
            }
            ksort($row);
            $rows[(int)($rowNode['r'] ?? (count($rows) + 1))] = $row;
        }
        $zip->close();
        $header = array_map(static fn($value) => strtolower(trim((string)$value)), $rows[1] ?? []);
        if ($header !== self::HEADERS) throw new RuntimeException('Template columns must exactly match: '.implode(' | ', self::HEADERS));
        return $rows;
    }

    private function birthday(string $value): ?string
    {
        if (is_numeric($value)) {
            $days = (int)floor((float)$value);
            if ($days < 1 || $days > 2958465) return null;
            return gmdate('Y-m-d', (int)(($days - 25569) * 86400));
        }
        foreach (['Y-m-d','Y/m/d','m/d/Y','n/j/Y','d/m/Y','d-m-Y','m-d-Y','d.m.Y','m.d.Y','Y-m-d H:i:s','m/d/Y H:i:s','Y-m-d\\TH:i:s'] as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $value);
            if ($date && $date->format($format) === $value) return $date->format('Y-m-d');
        }
        return null;
    }

    private static function columnName(int $number): string { $name=''; while ($number) { $number--; $name=chr(65+$number%26).$name; $number=intdiv($number,26); } return $name; }
    private static function columnIndex(string $name): int { $index=0; foreach(str_split($name) as $char) $index=$index*26+ord($char)-64; return $index; }
}
