     <div class="content">

         <?php if ( !empty($error) ):?>
         <span class="error"><?=$error?></span>
         <?php endif?>

         <form method="post" action="?c=auth&a=login">
             <div><span class="fld_caption">Логин</span><input type="text" id="login" name="login" value="<?=$sLogin?>" /></div>
             <div><span class="fld_caption">Пароль</span><input type="password" id="passw" name="passw"/></div>
             <input type="submit" value="Отправить"/>
         </form>

     </div>
