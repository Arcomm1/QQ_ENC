<div class="row" id="news">
    <div class="col-md-12">
        <div class="card border-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><?php echo lang('news'); ?></h4>
                <div class="btn-group">
                    <a class="btn btn-info" href="<?php echo site_url('news/index'); ?>"><?php echo lang('all'); ?></a>
                    <a class="btn btn-info" href="<?php echo site_url('news/index?type=announcement'); ?>"><?php echo lang('announcement'); ?></a>
                    <a class="btn btn-info" href="<?php echo site_url('news/index?type=document'); ?>"><?php echo lang('document'); ?></a>
                    <a class="btn btn-success" href="<?php echo site_url('news/create'); ?>"><?php echo lang('create'); ?></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-hover table-sm" id="tbl_news">
                        <thead>
                            <tr class="table-primary">
                                <th scope="col"><?php echo lang('type'); ?></th>
                                <th scope="col"><?php echo lang('name'); ?></th>
                                <th scope="col"><?php echo lang('date_lt'); ?></th>
                                <th scope="col" width="20%"><?php echo lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news as $n) { ?>
                                <tr>
                                    <td><?php echo lang($n->type); ?></td>
                                    <td><?php echo $n->title; ?></td>
                                    <td><?php echo $n->ends_at; ?></td>
                                    <td><a href="<?php echo site_url('news/edit/'.$n->id);?>" ><i class="fa fa-eye"></i></a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
