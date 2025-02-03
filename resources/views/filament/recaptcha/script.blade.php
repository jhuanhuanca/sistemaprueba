<script src="https://www.google.com/recaptcha/api.js?render=6Lcjei4qAAAAAHpve0tx4PDqkFaiQOn_8dZy_Bp9"></script>
<script>
function handleCaptchar(e){
    grecaptcha.ready(function(){
        grecaptcha.execute('6Lcjei4qAAAAAHpve0tx4PDqkFaiQOn_8dZy_Bp9',{action: 'login'})
        .then(function (token){
            this.set('captchaToken', token);
            this.login();
        });
    });
}

</script>