<form class="ScheduleList" action="<?=cn('ajax_action_multiple')?>">
    <div class="row">
        <div class="clearfix"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        <i class="fa fa-deaf" aria-hidden="true"></i> <?=l('Proxy management')?>
                    </h2>
                </div>
                <div class="header">
                    <div class="form-inline">
                        <div class="btn-group" role="group">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn bg-red waves-effect dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?=l('Action')?>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="btnActionModule" data-action="active" href="javascript:void(0);"><?=l('Active')?></a></li>
                                    <li><a class="btnActionModule" data-action="disable" href="javascript:void(0);"><?=l('Deactive')?></a></li>
                                    <li><a class="btnActionModule" data-action="delete" data-confirm="<?=l('Are you sure you want to delete this items?')?>" href="javascript:void(0);"><?=l('Delete')?></a></li>
                                </ul>
                            </div>
                            <a href="<?=cn('update')?>" class="btn bg-light-green waves-effect"><i class="fa fa-plus" aria-hidden="true"></i> <?=l('Add new')?></a>
                        </div>
                    </div>
                </div>
                <div class="body p0">
                    <table class="table table-bordered table-striped table-hover js-dataTable dataTable mb0">
                        <thead>
                            <tr>
                                <th style="width: 10px;">
                                    <input type="checkbox" id="md_checkbox_211" class="filled-in chk-col-red checkAll">
                                    <label class="p0 m0" for="md_checkbox_211">&nbsp;</label>
                                </th>
                                <th><?=l('Name')?></th>
                                <th><?=l('Proxy')?></th>
                                <th><?=l('Instagram Accounts')?></th>
                                <th><?=l('Created')?></th>
                                <th><?=l('Changed')?></th>
                                <th><?=l('Status')?></th>
                                <th><?=l('Option')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(!empty($result)){
                            foreach ($result as $key => $row) {
                            ?>
                            <tr class="pending" data-action="<?=cn('ajax_action_item')?>" data-id="<?=$row->id?>">
                                <td>
                                    <input type="checkbox" name="id[]" id="md_checkbox_<?=$key?>" class="filled-in chk-col-red checkItem" value="<?=$row->id?>">
                                    <label class="p0 m0" for="md_checkbox_<?=$key?>">&nbsp;</label>
                                </td>
                                <td><?=$row->name?></td>
                                <td><?=$row->proxy?></td>
                                <td><?=$row->ig_accounts?><a href="#" data-toggle="modal" class="btnActionViewProxyDetail" data-target="#proxyDetail"> <?=l("(View Detail)")?></a></td>
                                <td><?=date("h:i Y/m/d", strtotime($row->changed))?></td>
                                <td><?=date("h:i Y/m/d", strtotime($row->created))?></td>
                                <td style="width: 60px;">
                                    <div class="switch">
                                        <label><input type="checkbox" class="btnActionModuleItem" <?=$row->status==1?"checked":""?>><span class="lever switch-col-light-blue"></span></label>
                                    </div>
                                </td>
                                <td style="width: 80px;">
                                    <div class="btn-group" role="group">
                                        <a href="<?=cn('update?id='.$row->id)?>" class="btn bg-light-green waves-effect"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        <button type="button" class="btn bg-light-green waves-effect btnActionModuleItem" data-action="delete" data-confirm="<?=l('Are you sure you want to delete this item?')?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php }}?>
                        </tbody>
                    </table>   
                </div>
            </div>
        </div>
    </div>
</form>

<!--Modal Proxy Detail-->
<div id="proxyDetail" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="modal-header bg-owner" style="margin-bottom: 0px;padding-bottom: 0px;">
                        <button type="button" class="close btnActionCloseProxyDetail" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title"><?=l('List of instagram accounts on proxy')?></h3>
                    </div>
                    <div class="body table-responsive" style="padding: 4px 20px 20px;">
                        <hr>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th >No.</th>
                                    <th ><?=l('Instagram Accounts')?></th>
                                    <th ><?=l('Username')?></th>
                                    <th ><?=l('email')?></th>
                                    <th ><?=l('status')?></th>
                                </tr>
                            </thead>
                            <tbody id="proxy_list">
                                <!-- Insert proxy list -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


