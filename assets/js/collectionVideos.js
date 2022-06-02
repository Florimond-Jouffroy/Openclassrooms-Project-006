const getClosest = (elem, selector) => {
  for (; elem && elem !== window; elem = elem.parentNode) {
    if (elem.matches(selector)) return elem;
  }
  return null;
}

const applyRemoveVideoButtonEvent = videoButtonElt => {
  videoButtonElt.addEventListener("click", (e) => {
    let container = getClosest(e.target, "fieldset");

    if (container !== null) {
      container.remove();
    }
  });
}

Array.from(document.getElementsByClassName("delete-video-btn")).forEach(el => {
  applyRemoveVideoButtonEvent(el);
});

// Ont ajout un evenement sur le bouton add video
// pour qu'il aille chercher le prototype est le stock dans une div que l'on créer
Array.from(document.getElementsByClassName('add-video-btn')).forEach((el) => {
  el.addEventListener("click", () => {

    let target = document.getElementById(el.getAttribute('data-target'));

    let newEl = document.createElement("div");

    newEl.innerHTML = target.getAttribute("data-prototype").replace(/__name__label__/g, `Video ${target.childElementCount + 1}`).replace(/__name__/g, target.childElementCount + 1);
    newEl = newEl.firstChild;

    Array.from(newEl.getElementsByClassName("delete-video-btn")).forEach(el => {
      applyRemoveVideoButtonEvent(el);
    });

    target.appendChild(newEl);

  });
});
