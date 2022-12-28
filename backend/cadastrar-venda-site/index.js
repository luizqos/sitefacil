function abreFormularioCadastro(url) {
    window.open(url) ;
  }

  const urlParams = new URLSearchParams(window.location.search);
  const idProduto = urlParams.get('idProduto');

  if(idProduto > 1 && idProduto <= 4 ){
    if(idProduto == 2){
      document.getElementById("idProduto").value = idProduto;
      document.getElementById("plano").innerHTML = 'Plano Iniciante';
      document.getElementById("valorPlano").innerHTML = 23.00;
      document.getElementById("valorTotal").innerHTML = 23.00;
    }else if(idProduto == 3){
      document.getElementById("idProduto").value = idProduto;
      document.getElementById("plano").innerHTML = 'Plano Intermediario';
      document.getElementById("valorPlano").innerHTML = 26.00;
      document.getElementById("valorTotal").innerHTML = 26.00;
    }else if(idProduto == 4){
      document.getElementById("idProduto").value = idProduto;
      document.getElementById("plano").innerHTML = 'Plano Avançado';
      document.getElementById("valorPlano").innerHTML = 28.00;
      document.getElementById("valorTotal").innerHTML = 28.00;
    }
  }else{
    this.close();
  }

  function cadastraUsuario(){

  }