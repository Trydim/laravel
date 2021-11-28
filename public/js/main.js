var __webpack_exports__ = {};
/*!********************!*\
  !*** ./js/main.js ***!
  \********************/


const setLinkMenu = page => {
  let menu = f.qS('#sideMenu');
  if (!menu) return;

  let target = menu.querySelector('.nav-item.active');
  while (target) {
    let wrap = target.closest('[data-role="link"]');
    if (!wrap) return;
    target = wrap.previousElementSibling;
    target.click();
  }

  for (let n of [...menu.querySelectorAll('a')]) {
    let href = n.getAttribute('href') || '';
    if(href.includes(page)) { n.parentNode.classList.add('active'); break; }
  }
}

const cancelFormSubmit = () => {
  f.qA('form', 'keypress', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      return false;
    }
  });
}

const stopPreloader = () => {
  f.gI('preloader').remove();
  f.gI('mainWrapper').classList.add('show');
}

const setParentHeight = (target, height) => {
  const n = target.closest("ul[aria-expanded=\"false\"]");
  if (n) {
    n.style.height = (n.offsetHeight + height) + 'px';
    setParentHeight(n.parentNode, height);
  }
}

// Event function
// ---------------------------------------------------------------------------------------------------------------------

const cmsEvent = function() {
  let action = this.getAttribute('data-action');

  let select = {
    'menuToggle': () => {
      f.gI('mainWrapper').classList.toggle('menu-toggle');
      setTimeout(() => window.dispatchEvent(new Event('resize')), 200);
    },
    'exit' : () => {
      location.href = f.SITE_PATH + `?mode=auth&authAction=exit`;
    }
  };

  select[action] && select[action]();
};

const sideMenuExpanded = (e, node) => {
  e.preventDefault();

  const nodeS  = node.nextElementSibling,
        count  = nodeS.childElementCount,
        height = count * 50;//e.target.parentNode.offsetHeight;

  if( node.getAttribute('aria-expanded') === 'true'){
    node.setAttribute('aria-expanded', 'false');
    //setParentHeight(node, node.nextElementSibling.offsetHeight * -1);
    nodeS.style.height = nodeS.dataset.height;

    setTimeout(() => {
      nodeS.style.height = '0';
    }, 0);

  } else {
    node.setAttribute('aria-expanded', 'true');
    nodeS.dataset.height = height + 'px';
    nodeS.style.height = height + 'px';
    //setParentHeight(node, height);
    setTimeout(() => {
      nodeS.style.height = 'auto';
    }, 300);
  }
}

// Event bind
// -------------------------------------------------------------------------------------------------------------------

const onBind = () => {
// Block Authorization
  let node = f.gI(f.ID.AUTH_BLOCK);
  node && node.querySelectorAll('[data-action]')
              .forEach(n => n.addEventListener('click', cmsEvent));

  //if (node) return;
  // Menu Action
  f.qA('#sideMenu [role="button"]').forEach(n =>
    n.addEventListener('click', e => sideMenuExpanded(e, n))
  );

  node = f.qS('[data-action="menuToggle"]');
  node && node.addEventListener('click', cmsEvent);
}

// Entrance function
(() => {
  let page = (f.CURRENT_PAGE && !f.OUTSIDE) ? f.CURRENT_PAGE : 'public';

  if (page === '/' || f.gI('authForm')) return;
  cancelFormSubmit();
  onBind();
  f.dictionaryInit();

  setLinkMenu(page || '/');
  f.relatedOption();
  stopPreloader();

  setTimeout(() => { // todo разобраться с синхронизацией
    f.initShadow(); // todo убрать отсюда
  }, 100);
})();


//# sourceMappingURL=main.js.map