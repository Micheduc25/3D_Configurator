
function toggleToast(show, message, isError = false) {
  const toast = document.querySelector(".message-toast");

  if(message)
    toast.textContent = message;
    
  if(isError)toast.classList.toggle('error',true);
  else toast.classList.toggle('error',false);

  toast.classList.toggle(`show`, show);
}

function toggleBarrage(show) {
  const loaderContainer = document.querySelector(".barrage");
  loaderContainer.classList.toggle("show", show);
}
