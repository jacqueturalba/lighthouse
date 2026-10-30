<?php 
if (!empty($rows)) {
foreach($rows as $u) { ?>
<tr>
    <td><?= e($u['serial_no']) ?></td>
    <td><?= e($u['name']) ?></td>
    <td><?= e($u['email']) ?></td>
    <td><?= e($u['role']) ?></td>
    <td><?= e($u['status']) ?></td>
    <td><?= e($u['created_at']) ?></td>
    <td><?= e($u['last_login_at']?:'—') ?></td>
    <td class="d-flex justify-content-center">

        <div class="dropdown px-1">
            <button type="button" title="More actions" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i><span class="visually-hidden">More actions</span></button>
            <div class="dropdown-menu dropdown-menu-end p-1"><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#user-details-<?= (int)$u['id'] ?>"><i class="bi bi-person-vcard me-2"></i>View Details</button></div>
        </div>

        <div class="dropdown px-1">
            <button type="button" title="Reset Password" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                <i class="bi bi-arrow-clockwise"></i>
            </button>

            <form class="dropdown-menu p-4" method="post" action="/users/reset" style="margin:0; width:200px;">
                <?php echo form_token(); ?>
                <input type="hidden" name="email" value="<?= e($u['email']) ?>">
                <button class="btn btn-outline-primary py-1 mt-2 mb-2" alt="Send Password Reset Link">Send Reset Email</button>
            </form>

        </div>

        <div class="dropdown px-1">
            <button type="button" title="Update Password" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                <i class="bi bi-file-lock"></i>
            </button>

            <form class="dropdown-menu p-4" method="post" action="/users/password" style="margin:0; width:400px;">
                
                <input type="hidden" name="id" value="<?php echo (string)$u['id'];?>">
                <?php echo form_token(); ?>
                <input name="password" type="password" placeholder="New password" class="form-control form-control-sm mt-2 mb-1" required>
                <input name="confirm_password" type="password" placeholder="Confirm password" class="form-control form-control-sm mt-1 mb-2" required>
                <button class="btn btn-outline-primary py-1 mt-2 mb-2">Set password</button>
                
            </form>
        </div>

        <div class="dropdown px-1">
            <button type="button" title="Update Role" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                <i class="bi bi-person-gear"></i>
            </button>

            <form class="dropdown-menu p-4" method="post" action="/users/role" style="margin:0; width:400px;">
                <input type="hidden" name="id" value="<?php echo (string)$u['id']; ?>">
                <?php echo form_token(); ?>
                <select class="form-select form-select-sm mt-2 mb-2" name="role">
                    <option value="admin" <?php echo ($u['role']==='admin' ? ' selected' : '');?> >Admin</option>
                    <option value="super_admin" <?php echo ($u['role']==='super_admin' ? ' selected' : '');?> >Super Admin</option>
                </select>
                <button  class="btn btn-outline-primary py-1 mt-2 mb-2">
                    Update role
                </button>
            </form>
            
        </div>
    </td>
</tr>
<?php }
} else { ?><tr><td colspan="8" class="text-center text-secondary py-4">No users match this search.</td></tr><?php } ?>
