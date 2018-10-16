

<div class="back-image">

    <div class="back-branco">
    <div class="login-form" style="background: linear-gradient(217deg, #0f1011, rgba(255, 255, 255, 0) 69.71%), linear-gradient(127deg, #101313, rgba(0,255,0,0) 76.71%), linear-gradient(336deg, #2A2B34, rgba(0,0,255,0) 71.71%);position: relative;width: 99%;height: 100%;border-radius: unset;border: solid 5px;border-color: #fff;margin: auto;padding: unset;">
        <div>
        <ul class="login-nav" >
            <li class="login-top">
                <a href="<?=url("")?>" style="color:#000;" class="login-li"><?=l('Login')?></a>
            </li>
        
        </ul>
        <div class="clearfix"></div>

        <form action="<?=url('user_management/ajax_login')?>" data-redirect="<?=current_url()?>">
            <div style="width: 103%;height: 50px;border-radius: 25px;margin-bottom: 20px; background: #393d46;border: solid 1px;border-color: #4d525d;">
                <div class="input-border">
                    <div style="width: 40px;height: 40px;position: absolute;top: -4px;left: -39px;background: #6f7989;border-radius: 30px;">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"style="position: relative; top: 5px; left: 6px; color:#2e394f;"></i>
                        </span>
                    </div>
                    <div>
                        <input type="text" class="form-control" style="border:unset; max-width: 270px;left: 10px;" name="email" placeholder="<?=l('Email')?>" required autofocus>
                    </div>
                </div>
            </div>
            <div style="width: 103%;height: 50px;border-radius: 25px;margin-bottom: 20px; background: #393d46;border: solid 1px;border-color: #4d525d;">
                <div class="input-border">
                    <div style="width: 40px;height: 40px;position: absolute;top: -4px;left: -39px;background: #6f7989;border-radius: 30px;">
                        <span class="input-group-addon">
                            <i class="material-icons" style="position: relative; top: 5px; left: 6px; color:#2e394f;">lock</i>
                        </span>
                    </div>
                    <div >
                        <input type="password" class="form-control" name="password" placeholder="<?=l('Password')?>" style="border:unset; max-width: 270px;left: 10px;" required>
                    </div>
                </div>
            </div>
            <div class="input-group">
            <div class="another_action pull-left text-left">
                <input type="checkbox" id="md_checkbox_38" name="remember" class="filled-in chk-col-grey">
                <label for="md_checkbox_38" style="color: #aba6a7;"><?=l('Remember me')?></label><br/>
                <a href="<?=url("forgot_password")?>" style="color: #aba6a7;border-bottom: solid 1px;"><?=l('Forgot password')?></a>
            </div>
            <button type="submit" class="right bg-<?=THEME?> btnActionUpdate" style="border: solid 1px;border-radius: 25px; margin-top: 10px; 25px;font-size:  17px; width: 120px;">Entrar</button>
            
            <?php if(REGISTER_ALLOWED == 1){?>
            <button class="right bg-<?=THEME?>" style="border: solid 1px;border-radius: 25px; margin-top: 10px; 25px;font-size:  17px; width: 120px;">
                <a style="color:#393d46;" href="<?=base_url()?>painel/">Cadastrar</a>
            </button>
            <?php }?>

            </div>
            

            <!-- <?php if((FACEBOOK_ID != "" && FACEBOOK_SECRET != "") || (GOOGLE_ID != "" && GOOGLE_SECRET != "") || (TWITTER_ID != "" && TWITTER_SECRET != "")){?>
            <div class="clearfix"></div>
            <div class="login-social">
                <fieldset>
                    <legend><span><?=l('OR LOGIN VIA')?></span></legend>
                </fieldset>
                <div class="list-social">
                    <?php if(FACEBOOK_ID != "" && FACEBOOK_SECRET != ""){?>
                    <a href="<?=url("oauth/facebook")?>" title=""><img src="<?=BASE?>assets/images/btn-facebook.png" title="" alt=""></a>
                    <?php }?>
                    <?php if(GOOGLE_ID != "" && GOOGLE_SECRET != ""){?>
                    <a href="<?=url("oauth/google")?>" title=""><img src="<?=BASE?>assets/images/btn-google.png" title="" alt=""></a>
                    <?php }?>
                    <?php if(TWITTER_ID != "" && TWITTER_SECRET != ""){?>
                    <a href="<?=url("oauth/twitter")?>" title=""><img src="<?=BASE?>assets/images/btn-twitter.png" title="" alt=""></a>
                    <?php }?>
                </div>
            </div>
            <?php }?> -->
            
        </form>
        
        </div>
        <div class="copyright">2018 &#169;   Todos os direitos reservados.</div>
    </div>
    </div>
    </div>
