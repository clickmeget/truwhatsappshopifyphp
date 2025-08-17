
<div class="fixed right-px bottom-12 flex p-2 items-center">
        <img src="<?=$base_url;?>assets/images/Group 733.svg" alt="">
        <a href="https://api.whatsapp.com/send?phone=923212428472" target="_blank"><img src="<?=$base_url;?>/assets/images/Group 730.svg" alt=""></a>
    </div>

    <footer class="bg-[#43CD66] fixed inset-x-0 bottom-0">
        <nav class="mx-auto flex space-x-4 max-w-full items-center justify-between p-2">
            <ul class="text-center">
                <li class="list-none inline-block"><a href="<?=$base_url;?>dosndonts/?shop=<?=$shop;?>" class="no-underline text-white px-2 font-medium text-sm">Dos & don&apos;ts</a></li>
                <li class="list-none inline-block"><a href="<?=$base_url;?>privacy-policy?shop=<?=$shop;?>" class="no-underline text-white px-2 font-medium text-sm">Privacy Policy</a></li>
            </ul>
            <a href="https://geca.co?utm_source=tru_wa" target="_blank"><img src="<?=$base_url;?>/assets/images/footer-logo.svg" alt="" class="h-9 w-auto"></a>
            <ul class="text-center">
                <li class="list-none inline-block"><a href="https://www.facebook.com/global.ecommerce.agency" class="no-underline font-medium"><img src="<?=$base_url;?>/assets/images/facebook.svg" class="h-4 px-5 "></a></li>
                <li class="list-none inline-block"><a href="#" class="no-underline font-medium"><img src="<?=$base_url;?>/assets/images/insta.svg" class="h-4 px-5 "></a></li>
                <li class="list-none inline-block"><a href="#" class="no-underline font-medium"><img src="<?=$base_url;?>/assets/images/linkedin.svg" class="h-4 px-5 "></a></li>
            </ul>
        </nav>
    </footer>
    
     <?php
     if(!isset($store_status))
     {
        ?>
            <script src="<?=$base_url;?>assets/js/script.js?v=<?=md5(rand(555,999));?>" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <?php
     }
     ?>

</body>
</html>