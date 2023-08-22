<div class="container-lg mt-3" id="users">
    <div class="col-md-12">
        <div class="card border-top-info border-info border-top-3">
            <div class="card-header">
                <div class="row">
                    <div class="col d-flex justify-content-between">
                        <h4><?php echo lang('manage_users'); ?></h4>
                        <a class="btn btn-success float-right" href="<?php echo site_url('users/create'); ?>"><?php echo lang('create_user'); ?></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                    <tr class="table-primary">
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Last login</th>
                        <th scope="col" width='10%'>Action</th>
                    </tr>
                    </thead>
                    <?php foreach ($users as $u) {
                        if ($u->enabled != 'yes')
                             { echo "<tr class='table-secondary'>"; }
                        else { echo "<tr>"; }
                        echo "<td>".$u->name."</td>";
                        echo "<td>".$u->email."</td>";
                        echo "<td>".lang($u->role)."</td>";
                        echo "<td>".$u->last_login."</td>";
                        echo "<td><a href=".site_url("users/edit/$u->id")."><i class='cil-pencil'></i></a></td>";
                        echo "</tr>";
                    } ?>
                </table>
            </div>
        </div>
    </div>
</div>
