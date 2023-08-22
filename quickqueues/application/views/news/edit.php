<div class="row" id="edit_post">
    <div class="col-md-12">
        <div class="card border-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><?php echo lang('news'); ?></h4>
                <div class="btn-group">
                    <a class="btn btn-primary" href="<?php echo site_url('news'); ?>"><?php echo lang('back'); ?></a>
                    <a class="btn btn-danger" href="<?php echo site_url('news/delete/'.$article->id); ?>"><?php echo lang('delete'); ?></a>
                </div>
            </div>
            <div class="card-body">
                <?php echo form_open_multipart(); ?>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php echo lang('type'); ?></span>
                            </div>
                            <select class="form-control" id="type" name="type">
                                <option <?php if ($article->type == 'announcement') { echo "Selected"; } ?> value="announcement"><?php echo lang('announcement'); ?></option>
                                <option <?php if ($article->type == 'document') { echo "Selected"; } ?> value="document"><?php echo lang('document'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php echo lang('name'); ?></span>
                            </div>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo $article->title; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="content" name="content"><?php echo $article->content; ?></textarea>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php echo lang('date_lt'); ?></span>
                            </div>
                            <input type="text" class="form-control" id="ends_at" name="ends_at" value="<?php echo $article->ends_at; ?>">
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <input class="form-control" type="file" id="userfile" name="userfile">
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-sm">
                                <?php foreach ($files as $f) { ?>
                                    <tr>
                                        <td><?php echo $f; ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-success" href="<?php echo base_url('uploads/'.$f); ?>"><?php echo lang('download'); ?></a>
                                            <a class="btn btn-sm btn-danger" href="<?php echo site_url('news/delete_file/'.$f); ?>"><?php echo lang('delete'); ?></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary"><?php echo lang('save'); ?></button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

</div>
