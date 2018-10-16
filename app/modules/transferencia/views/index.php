<form class="formListModule" action="<?=url('transferencia/transferir')?>"  method="post">
    <div class="row">
        <div class="clearfix"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="valor">Valor</label><br>
                            <input class="imput-money" type="number" step="0.01"  name="valor">
                        </div>
                        <div class="form-group">
                            <label for="destinatario">Destinatario</label>
                            <input class="imput-money" type="text" name="destinatario"></form>
                        </div>
                        <button type="submit" class="but-money">Enviar</button>
                    </div>
                    <div class="col-md-7 text-center">
                        
                    </div>
                </div>
                <div class="saques row">
                    <div class="col-md-5" style="left: 6%;">
                    <h4 style="position: relative;left: 10%;">Saldo </h4>
                        <div class="title-saq">Disponivel:  <img src="<?=base_url() ?>assets/images/adcashverde.png" alt="" style="width: 19px;position: relative;top: -2px;"> <h5 style="display: inline;color: #6bc078;"><?=  $saldo->disponivel ?> </h5></div>
                        <div class="title-saq">Acumulado:   <img src="<?=base_url() ?>assets/images/adcash.png" alt="" style="width: 3%;position: relative;top: -2px;"> <h5 style="display: inline;"><?=  $saldo->acomulado ?> </h5></div>
                        <div class="title-saq">Bloqueado:   <img src="<?=base_url() ?>assets/images/adcash.png" alt="" style="width: 3%;position: relative;top: -2px;"> <h5 style="display: inline;"><?=  $saldo->bloqueado ?> </h5></div>
                        <div class="title-saq">Gasto:   <img src="<?=base_url() ?>assets/images/adcashvermelho.png" alt="" style="width: 4%;position: relative;top: -2px;"> <h5 style="display: inline;     color: #ff0606;"><?=  $saldo->gasto ?> </h5></div>
                        <div class="title-saq">Sacado:   <img src="<?=base_url() ?>assets/images/adcash.png" alt="" style="width: 3%;position: relative;top: -2px;"> <h5 style="display: inline;"><?=  $saldo->sacado   ?></h5></div>
                    </div>
                    <div class="col-md-7 text-center">
                        <h4>Saques efetuados</h4>
                        <h5>100,00 10/03/2018</h5>
                        <h5>100,00 10/03/2018</h5>
                        <h5>100,00 10/03/2018</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>