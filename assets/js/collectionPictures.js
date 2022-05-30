const getClosest = (elem, selector) => {
  for (; elem && elem !== window; elem = elem.parentNode) {
    if (elem.matches(selector)) return elem;
  }
  return null;
}

const applyRemovePictureButtonEvent = pictureButtonElt => {
  pictureButtonElt.addEventListener("click", (e) => {
    let container = getClosest(e.target, "fieldset");

    if (container !== null) {
      container.remove();
    }
  });
}

Array.from(document.getElementsByClassName("delete-picture-btn")).forEach(el => {
  applyRemovePictureButtonEvent(el);
});

// Ont ajout un evenement sur le bouton add picture
// pour qu'il aille chercher le prototype est le stock dans une div que l'on créer
Array.from(document.getElementsByClassName("add-picture-btn")).forEach((el) => {
  el.addEventListener("click", () => {

    let target = document.getElementById(el.getAttribute('data-target'));

    let newEl = document.createElement('div');

    newEl.innerHTML = target.getAttribute("data-prototype").replace(/__name__label__/g, `Fichier ${target.childElementCount + 1}`).replace(/__name__/g, target.childElementCount + 1);
    newEl = newEl.firstChild;

    Array.from(newEl.getElementsByClassName("delete-picture-btn")).forEach(el => {
      applyRemovePictureButtonEvent(el);
    })

    target.appendChild(newEl);

  });
});
