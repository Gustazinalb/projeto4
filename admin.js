const mostrarSenha = document.getElementById("mostrarSenha");
const senha = document.getElementById("senha");

mostrarSenha.addEventListener("click", ()=>{

    if(senha.type=="password"){

        senha.type="text";
        mostrarSenha.innerHTML='<i class="fa-solid fa-eye-slash"></i>';

    }else{

        senha.type="password";
        mostrarSenha.innerHTML='<i class="fa-solid fa-eye"></i>';

    }

});