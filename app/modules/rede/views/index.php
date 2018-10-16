<form class="formListModule" action="<?=url('rede/nivel')?>"  method="post">
    <div class="row">
        <div class="clearfix"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div style="text-align: center;position: absolute;left: 80%;top: 15%;"><?php if($nivel == 2 ){ ?>
                    <a href="javascript:history.back()" style="font-size: 170%;  color: #4f6082;"><i class="glyphicon glyphicon-arrow-up" style="position: relative;left: -2%;font-size: 140%;"></i><br>1° Nivel</a>
                    <?php } ?>
                    <?php if($nivel == 3 ){ ?>
                    <a href="javascript:history.back()" style="font-size: 170%;  color: #4f6082;"><i class="glyphicon glyphicon-arrow-up" style="position: relative;left: -2%;font-size: 140%;"></i><br>2° Nivel</a>
                    <?php } ?></div>
                    <h2>
                        <img src="<?=$user->user_image?>" class="img-myperfil">
                        
                        <h2  class="name-perf"><?=$user->fullname?></h2>
                        <h2  class="codi-perf"><?=$user->codigoIndicacao?></h2>
                        
                    </h2>
                    
                    
                </div>
                <!-- <div class="header">
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
                </div> -->
                <div style="background: linear-gradient(to right,#000000, #3b3938);width: 87%;position: relative;left: 6%;color: #fff;border-radius: 1%;">
                <h3 style="position: relative;left: 2%; top: 5px;"> <?=$nivel?>°Nivel</h3>
                <div class="body p0">
                    <table class="table table-bordered table-striped table-hover js-dataTableSchedule mb0" style="width: 98%;position: relative;left: 1%;">
                        <thead>
                            <tr style="background: #aaaaaa;color: #fff;">
                                <th>Perfil</th> 
                                <th>Codigo de Indicação</th> 
                                <th>Email</th>
                                <th>Cadastro</th>
                                <?php if($nivel <= 2) { ?>
                                    <th>Ver</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            
                            foreach ($result as $key => $row) {
                            ?>
                            <tr class="pending" data-action="<?=cn('nivel')?>" data-id="<?=$row->id?>">
                                
                                <td ><img src="<?=$row->user_image?>" style="border-radius: 50%;max-width: 100px;width: 22%;height: 50px;max-height: 100px;margin-right: 10px;"><h6 style="display: -webkit-inline-box;max-width: 149px;"><?=$row->fullname?></h6></td>
                                <td style="vertical-align: middle;text-align: center;"><?=$row->codigoIndicacao?></td>
                                <td style="vertical-align: middle;text-align: center;"><?=$row->email?></td>
                                <td style="vertical-align: middle;text-align: center;"><?=date("h:i Y/m/d", strtotime($row->created))?></td>
                                <?php if($nivel <= 2) { ?>
                                    <td style="width: 60px;text-align: center;vertical-align: middle;">
                                        <?php $proximo_nivel = $nivel + 1;?>
                                        <a href="<?=url("rede?usuario=$row->codigoIndicacao&nivel=$proximo_nivel")?>"><img src="<?=base_url()?>assets/images/olinho.png" style="width: 55%;"></a>
                                    </td>
                                <?php } ?>
                                
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>   
                </div>
                </div>
            </div>
        </div>
    </div>
</form>