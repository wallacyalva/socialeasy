<form class="formListModule" action="<?=cn('ajax_action_multiple')?>">
    <div class="row">
        <div class="clearfix"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        <i class="fa fa-instagram" aria-hidden="true"></i> <?=l('Instagram accounts')?>
                    </h2>
                    <p>Para o funcionamento do , é necessário desativar sua conta do instagram pelo período de duas horas, só então cadastra-la aqui.ela sera ativada automaticamente pelo , obrigado.</p>
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
                            <a href="javscript:void(0);" class="btn bg-light-green waves-effect" data-toggle="modal" data-target="#modal-add-account"><i class="fa fa-plus" aria-hidden="true"></i> <?=l('Add new')?></a>
                        </div>
                    </div>
                </div>
                <div class="body p0">
                    <table class="table table-bordered table-striped table-hover js-dataTableSchedule mb0">
                        <thead>
                            <tr>
                                <th style="width: 10px;">
                                    <input type="checkbox" id="md_checkbox_211" class="filled-in chk-col-red checkAll">
                                    <label class="p0 m0" for="md_checkbox_211">&nbsp;</label>
                                </th>
                                <?php if(IS_ADMIN == 1){?>
                                <th><?=l('User')?></th>
                                <?php }?>
                                <!-- <th><?=l('Instagram ID')?></th> -->
                                <th><?=l('Username')?></th> 
                                <th class="text-center"><?=l('Update Token')?></th>
                                <th><?=l('Status')?></th>
                                <th><?=l('Option')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(!empty($result)){
                            foreach ($result as $key => $row) {
                            ?>
                            <tr class="pending" data-action="<?=cn('ajax_action_item')?>" data-action-groups="<?=cn('ajax_get_groups')?>" data-id="<?=$row->id?>">
                                <td style="text-align: center;">
                                    <input type="checkbox" name="id[]" id="md_checkbox_<?=$key?>" class="filled-in chk-col-red checkItem" value="<?=$row->id?>">
                                    <label class="p0 m0" for="md_checkbox_<?=$key?>">&nbsp;</label>
                                </td>
                                <?php if(IS_ADMIN == 1){?>
                                <td style="text-align: center;"><a href="<?=url('user_management/update?id='.$row->uid)?>"><?=$row->user?></a></td>
                                <?php }?>
                                <!-- <td><a href="https://Instagram.com/<?=$row->username?>" target="_blank"><?=$row->fid?></a></td> -->
                                <td style="text-align: center;"><a href="https://Instagram.com/<?=$row->username?>" target="_blank"><?=$row->username?></a></td>
                                <td class="text-center"><button type="button" class="btn bg-blue waves-effect btnUpdateGroups" data-type="page" data-id="<?=$row->id?>" data-action-groups="<?=cn('ajax_get_groups')?>"><?=l('Update Token')?></button></td>
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