    <div class="row">
        <div class="clearfix"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #fff;box-shadow: 0 2px 10px rgba(0, 0, 0, 0.29);border-radius: 10px;">
            <div class="card" style="min-height: unset;box-shadow: none;">
                <div class="header">
                    <form class="formListModule" action="<?=url('saque/sacar')?>"  method="post">  
                    <h4>Solicitar Saque</h4>
                    <div class="col-md-5" style="margin-top: 20px;margin-bottom: 20px;border-right: solid 1px;border-color: #ededed;">
                        <h5 class="top-imput">Valor de Saque:</h5>
                        <input type="number" class="imput-money" name="money" placeholder="0,00" ><br>
                        <h6 style="color: #f00;">lembre-se que somente valores acima de R$ 100 </h6>
                        <button class="but-money">Efetuar Saque</button>
                    </div>
                    </form>
                    <div class="col-md-7" style="margin-top: 20px;margin-bottom: 20px;">
                        <h4>Saldo </h4>
                        <h5 style="color: #000;">Disponivel:  <div style="display: inline;color: #25d366;">R$ <?=  $saldo->disponivel ?></div></h5>
                        <h5 style="color: #000;">Sacado: R$ <div style="display: inline;"><?=  $saldo->sacado  ?></div></h5>
                    </div>
                </div>
                <div class="col-md-12" style="margin-bottom: 5%;">
                    <h4 style="border-bottom: solid 1px;border-color: #ededed;">Informações do usuario</h4>
                    <!-- user -->
                   <div>
                        <div class="info-user">Nome: <h6 class="info-name"><?= $user->fullname ?></h6></div>
                        <div class="info-user">Email: <h6 class="info-name"><?= $user->email ?></h6></div>
                        <div class="info-user">Telefone<h6 class="info-name">(<?= $user->DDD ?>)<?= $user->whats ?></h6></div>
                   </div>
                   <div>
                        <div class="info-user">Cpf: <h6 class="info-name"><?= $user->cpf ?></h6></div>
                        <div class="info-user">Data de nascimento: <h6 class="info-name"><?= $user->data_nascimento ?></h6></div>
                   </div>
                </div>
                    <div style="display: table; width: 100%;">
                
                        <h3 style="border-bottom: solid 1px;border-color: #ededed;">informações bancárias:</h3>
                        <label for="coco" class="info-user">Numero do banco:</label>
                        <input class="info-name" name="banco" type="number" min="0" max="999" placeholder="<?= $conta->numeroBanco ?>" >
                        <label for="coco" class="info-user">Agencia bancaria:</label>
                        <input  class="info-name"name="agencia" type="number" min="0" max="9999"  placeholder="<?= $conta->agencia ?>" >
                        <label class="info-user" for="coco">Conta:</label>
                        <input  class="info-name" name="conta" type="number" min="0" max="9999999999"  placeholder="<?= $conta->conta ?>" >
                        <button class="but-money" action="<?=url('saque/salvarConta')?>" style="margin-top: 2%;">Salvar conta</button>
                    
                    </div>
                <div class="col-md-10" style="position: relative;left: 8.3%;top: 15px;margin-bottom: 60px;background: linear-gradient(45deg, #070707 0%, #323232 100%);border-radius: 10px;">
                    <table style="width: 100%;">
                    <tr class="titulos">
                        <th class="titulo">valor</th>
                        <th class="titulo">pagante</th>
                        <th class="titulo">recebedor</th>
                        <th class="titulo">razao</th>
                    </tr>
                    <?php foreach ($extratos as $extrato) { ?>
                        <tr class="corpo">
                            <td class="titulo"> <?=$extrato->valor ?></td>
                            <td class="titulo"><?=$extrato->nome_pagante ?></td>
                            <td class="titulo"><?=$extrato->nome_recebedor ?></td>
                            <td class="titulo"><?=$extrato->razao ?></td>
                        </tr>
                    <?php } ?>
                    </table>
                    
                        
                </div>
               
                
            </div>
        </div>
    </div>
