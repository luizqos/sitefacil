function is_name(e) {
  let re = /\b[A-Za-zÀ-ú][A-Za-zÀ-ú]+,?\s[A-Za-zÀ-ú][A-Za-zÀ-ú]{2,19}\b/gi;

  if(!(re.test(e.value)) && e.value.length > 0){
    showToast('Digite o seu nome completo', 'error');
  }
  return true;
}
