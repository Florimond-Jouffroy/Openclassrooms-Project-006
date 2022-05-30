
let btnLoad = document.getElementById('loadMedia');
let btnHide = document.getElementById('hideMedia');
let medias = document.getElementById('medias');

btnLoad.addEventListener('click', function () {
  medias.className = '';
  btnLoad.className = 'd-md-none d-lg-none d-none';
  btnHide.className = 'd-md-block d-lg-none';
});

btnHide.addEventListener('click', function () {
  medias.className = 'd-none';
  btnLoad.className = 'd-md-block d-lg-none';
  btnHide.className = 'd-md-none d-lg-none d-none';
})
