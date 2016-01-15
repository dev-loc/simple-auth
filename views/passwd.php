     <div class="content">

         <div>
            <a href="<?=$sBaseUrl?>?c=userpage&a=index">главная</a> | 
            <a href="<?=$sBaseUrl?>?c=userpage&a=passwd">поменять пароль</a> | 
            <a href="<?=$sBaseUrl?>?c=auth&a=logout">выйти</a>
         </div>

         <h3>поменять пароль</h3> 

         <?php if ( !empty($error) ):?>
         <span class="error"><?=$error?></span>
         <?php endif?>

         <form method="post" action="<?=$sBaseUrl?>?c=userpage&a=passwd">
             <div><span class="fld_passw">Старый пароль</span><input type="password" name="old_passw" autocomplete="off"/></div>
             <div><span class="fld_passw">Новый пароль</span><input type="password" name="passw1" autocomplete="off"/></div>
             <div><span class="fld_passw">Повторить пароль</span><input type="password" name="passw2" autocomplete="off"/></div>
             <input type="submit" value="Сохранить"/>
         </form>

     </div>
