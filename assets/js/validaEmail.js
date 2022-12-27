function is_email(e) {
  let re = /\S+@\S+\.\S+/;
  let espace = /( )+/;


  if (espace.test(e.value) === true) {
    showToast('Email digitado com Espaço', 'error');
  } else if (re.test(e.value) === false && e.value.length > 0) {
      showToast('Email informado não é válido', 'error');
    }

  return true;
}
