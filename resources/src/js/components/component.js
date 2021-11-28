// МОДУЛИ
//----------------------------------------------------------------------------------------------------------------------

import {c} from "./const.js";
import {f} from "./func.js";
import {q} from "./query.js";

// Загрузчик / preLoader
export class LoaderIcon {
  constructor(field, showNow = true, targetBlock = true, param = {}) {
    this.node = typeof field === 'string' ? f.qS(field) : field;
    if (!(this.node instanceof HTMLElement)) return;
    //this.block         = targetBlock;
    this.customWrap    = param.wrap || false;
    this.customLoader  = param.loader || false;
    this.customLoaderS = param.loaderS || false;
    this.big           = !param.small || true;
    showNow && this.start();
  }

  setParam() {
    let coords = this.node.getBoundingClientRect();

    this.big = coords.height > 50;
    this.loaderNode = this.getTemplateNode();

    this.loaderNode.style.top    = coords.y + pageYOffset + 'px';
    this.loaderNode.style.left   = coords.x + pageXOffset + 'px';
    this.loaderNode.style.height = coords.height + 'px';
    this.loaderNode.style.width  = coords.width + 'px';
  }

  start() {
    if (this.status) return;
    this.status = true;

    this.setParam();
    this.big && (this.node.style.filter = 'blur(5px)');
    document.body.style.position = 'relative';
    document.body.append(this.loaderNode);
  }

  stop() {
    if (!this.status) return;
    this.status = false;

    this.big && (this.node.style.filter = '');
    document.body.style.position = '';
    this.loaderNode.remove();
  }

  getTemplateNode() {
    let n = document.createElement('div');
    n.innerHTML = this.templateWrap();
    return n.children[0];
  }

  templateWrap() {
    let node = this.big ? this.template() : this.templateSmall();
    return this.customWrap || `<div style="display: flex;align-items: center;justify-content: center;position:fixed;">${node}</div>`;
  }

  template() {
    let template = `
    <style>
    .letter-holder {
      padding: 16px;
    }
    .letter {
      float: left;
      font-size: 14px;
      color: #777;
    }
    .load-6 .letter {
      animation-name: loadingF;
      animation-duration: 1.6s;
      animation-iteration-count: infinite;
    }
    .l-1 {
      animation-delay: 0.48s;
    }
    .l-2 {
      animation-delay: 0.6s;
    }
    .l-3 {
      animation-delay: 0.72s;
    }
    .l-4 {
      animation-delay: 0.84s;
    }
    .l-5 {
      animation-delay: 0.96s;
    }
    .l-6 {
      animation-delay: 1.08s;
    }
    .l-7 {
      animation-delay: 1.2s;
    }
    .l-8 {
      animation-delay: 1.32s;
    }
    .l-9 {
      animation-delay: 1.44s;
    }
    .l-10 {
      animation-delay: 1.56s;
    }
    @keyframes loadingF {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 1;
      }
    }
    </style>
    <div class="load-6">
      <div class="letter-holder">
        <div class="l-1 letter">L</div>
        <div class="l-2 letter">o</div>
        <div class="l-3 letter">a</div>
        <div class="l-4 letter">d</div>
        <div class="l-5 letter">i</div>
        <div class="l-6 letter">n</div>
        <div class="l-7 letter">g</div>
        <div class="l-8 letter">.</div>
        <div class="l-9 letter">.</div>
        <div class="l-10 letter">.</div>
      </div>
    </div>
    `;
    return this.customLoader || template;
  }

  templateSmall () {
    let defSmallLoader = `
    <style>
      .load-3 .line {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 15px;
        background-color: #4b9cdb;
      }
      .load-3 {
        height: 30px;
        margin-top: -5px;
      }
      .load-3 .line:nth-last-child(1) {
        animation: loadingC 0.6s 0.1s linear infinite;
      }
      .load-3 .line:nth-last-child(2) {
        animation: loadingC 0.6s 0.2s linear infinite;
      }
      .load-3 .line:nth-last-child(3) {
        animation: loadingC 0.6s 0.3s linear infinite;
      }
      @keyframes loadingC {
        0 {
          transform: translate(0, 0);
        }
        50% {
          transform: translate(0, 15px);
        }
        100% {
          transform: translate(0, 0);
        }
      }
    </style>
    <div class="load-3">
      <div class="line"></div>
      <div class="line"></div>
      <div class="line"></div>
    </div>
    `;

    return this.customLoaderS || defSmallLoader;
  }
}

// Всплывающее сообщение
export class MessageToast {
  constructor() {
    this.messageBlock = document.createElement("div");
    this.messageBlock.classList.value = 'position-fixed top-0 start-50 translate-middle-x align-items-center p-3 w-50 fade';
    this.messageBlock.setAttribute('type', 'button');
    this.messageBlock.style.zIndex = '1080';
    document.body.append(this.messageBlock);
  }

  checkMsq(msg, type) {
    if (!type) {
      this.setMessage(type);
      this.setColor('error');
    } else {
      this.setMessage(msg);
      this.setColor('success');
    }
  }

  setMessage(msg) {
    this.messageBlock.innerHTML = `<div class="row">
      <div class="col-11 text-center">${msg || 'Текст сообщения пустой'}</div>
      <button type="button" class="col-1 btn-close btn-close-white m-auto"></button>
    </div>`;
  }

  setColor(color) {
    this.messageBlock.classList.remove('bg-success', 'bg-warning', 'bg-danger');
    switch (color) {
      case 'success':
      default:
        this.messageBlock.classList.add('bg-success');
        break;
      case 'warning':
        this.messageBlock.classList.add('bg-warning');
        break;
      case 'error':
        this.messageBlock.classList.add('bg-danger');
        break;
    }
    setTimeout(() => this.messageBlock.classList.add('show', 'pi-white'), 50);
  }

  show(msg = 'message body', type = 'success', autoClose = true) {
    const close = (delay = 3000) => {
      setTimeout(() => {
        this.messageBlock.classList.remove('show');
        setTimeout( () => this.messageBlock.remove(), 300);
      }, delay);
    }

    if (typeof type !== 'string') this.checkMsq(msg, type); else {
      this.setMessage(msg);
      this.setColor(type);
    }

    if (autoClose) close();
    else this.messageBlock.addEventListener('click', close.bind(this, 0), {once: true});

    return this.messageBlock;
  }
}

// Печать
export const Print = () => {
  let p   = Object.create(null);
  p.frame = document.createElement("iframe");
  p.data  = 'no content'; // html

  p.frame.onload = function () {
    history.pushState({print: 'ok'}, '', '/');
    this.sandbox  = 'allow-modals';
    this.contentDocument.body.append(p.data);
    this.contentWindow.print();
  }

  p.loadImage = link => new Promise(resolve => {
    fetch(link).then(data => data.blob()).then(data => {
      let reader = new FileReader();

      reader.onloadend = () => {
        let img = document.createElement('img');
        img.style.width = '100%';
        img.src = reader.result;
        resolve(img);
      }

      reader.readAsDataURL(data);
    });
  });

  p.prepareContent = async function (container) {
    let nodes = container.querySelectorAll('img'),
        imagesPromise = [];

    nodes.forEach(n => {
      !n.src.includes('base64') && imagesPromise.push(this.loadImage(n.src));
    })

    imagesPromise.length
    && await Promise.all([...imagesPromise])
                    .then(value => nodes.forEach((n, i) => n.src = value[i].src));

    return container;
  }

  p.setContent = async function (content, classList = []) {
    let container = document.createElement('div'), cloneN, delay = 0,
        haveImg = content.includes('<img');
    classList.map(i => container.classList.add(i));
    container.innerHTML = content;
    if(haveImg) {
      container = await this.prepareContent(container);
    }
    this.data = container;
  }

  p.print = function (content, printStyleTpl = 'printTpl.css', classList = []) {
    q.Get({
      data: 'mode=docs&docsAction=getPrintStyle&fileTpl=' + printStyleTpl
    }).then(async data => {
      const scrollY = window.pageYOffset;

      typeof data.style === 'string' && (content = `<style>${data.style}</style>` + content);
      await this.setContent(content, classList);

      document.body.append(this.frame);
      this.frame.remove();
      window.scrollTo(0, scrollY);
    });
  }

  /**
   * Печатать используя фукнцию
   * @param printFunc
   * @param data
   * @param type
   * @return {Promise<void>}
   */
  p.orderPrint = async function (printFunc, data, type) {
    let report = JSON.parse(data.order['reportValue']);
    this.print(await printFunc(type, report));
  }

  return p;
}

// Поиск
export const Searching = () => {
  const obj = Object.create(null);

  obj.init = function (param) {
    let {popup = true, node, searchData,
          finishFunc = () => {},
          showResult = () => {}} = param,
        func = (e) => this.searchFocus(e);

    this.usePopup = popup; // Показывать результаты в сплывающем окне
    this.searchData = searchData;
    this.resultFunc = (index) => finishFunc(index);
    this.returnFunc = (resultIds) => showResult(this.resultTmp, resultIds);

    node.removeEventListener('focus', func);
    node.addEventListener('focus', func);
    node.dispatchEvent(new Event('focus'));
  }

  obj.setSearchData = function (data) {
    this.searchData = data;
    return this;
  }

  // Переделать когда нить. в вордпрессе очень крутой поисковик
  obj.search = function (need) {
    let pattern     = /(-|_|\(|\)|@)/gm,
        cyrillic    = 'УКЕНХВАРОСМТукенхваросмт',
        latin       = 'YKEHXBAPOCMTykehxbapocmt',
        //cyrillicKey = 'ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮйцукенгшщзхъфывапролджэячсмитьбю',
        //latinKey    = 'QWERTYUIOP{}ASDFGHJKL:\"ZXCVBNM<>qwertyuiop[]asdfghjkl;\'zxcvbnm,.',
        replacerLC    = (match) => latin.charAt(cyrillic.indexOf(match)),
        replacerCL    = (match) => cyrillic.charAt(latin.indexOf(match)),
        //replacerKeyLC = (match) => latinKey.charAt(cyrillicKey.indexOf(match)),
        //replacerKeyCL = (match) => cyrillicKey.charAt(latinKey.indexOf(match)),
        lettersL = new RegExp(`(${latin.split('').join('|')})`, 'gi'),
        lettersC = new RegExp(`(${cyrillic.split('').join('|')})`, 'gi');
    //funcKeyL = new RegExp(`(${latinKey.split('').join('|')})`, 'gi'),
    //funcKeyC = new RegExp(`(${cyrillicKey.split('').join('|')})`, 'gi');

    need = need.replace(pattern, '');
    if (need.includes(' ')) need += '|' + need.split(' ').reverse().join(' ');

    let regArr = [], i, test;

    (i = need.replace(lettersL, replacerCL).replace(/ /gm, '.+')) && regArr.push(i);
    (i = need.replace(lettersC, replacerLC).replace(/ /gm, '.+')) && regArr.push(i);
    //(i = need.replace(funcKeyL, replacerKeyCL).replace(/ /gm, '.+')) && regArr.push(i);
    //(i = need.replace(funcKeyC, replacerKeyLC).replace(/ /gm, '.+')) && regArr.push(i);
    //i = `${regArr.join('|')}`;
    test = new RegExp(`${regArr.join('|')}`, 'i');

    return Object.entries(this.searchData)
                 .map(i => test.test(i[1].replace(pattern, '')) && i[0]).filter(i => i);
  }

  obj.clear = function (inputNode) {
    inputNode.removeEventListener('keyup', this.bindInputNodeEvent);
    setTimeout(() => {
      this.usePopup && this.resultTmp.remove();
    }, 0);
  }

  // Events
  const inputNodeEvent = function (e) {
    let value = e.target.value;
    if(value.length > 1) {
      f.show(this.resultTmp);
      this.returnFunc(this.search(value));
    } else {
      f.hide(this.resultTmp);
      this.returnFunc(Object.keys(this.searchData));
    }
    e.key === 'Enter' && e.target.dispatchEvent(new Event('blur')) && e.target.blur();
  }

  obj.searchFocus = function (e) {
    let target = e.target,
        wrap = target.parentNode;

    if(this.usePopup && !this.resultTmp) {
      this.resultTmp = f.gTNode('#searchResult');
      this.resultTmp.addEventListener('click', (e) => this.clickResult(e, target));
    }

    this.bindInputNodeEvent = inputNodeEvent.bind(this);
    target.addEventListener('keyup', this.bindInputNodeEvent);
    target.addEventListener('blur', () => setTimeout(() => this.clear(target), 100), {once: true});

    if(this.usePopup) {
      wrap.style.position = 'relative';
      wrap.append(this.resultTmp);
    }

    target.dispatchEvent(new Event('keyup'));
  }

  obj.clickResult = function (e, inputNode) {
    if(this.resultTmp === e.target) return;
    let index = +e.target.dataset.id;

    this.clear(inputNode);
    //inputNode.value = this.data[index].name;
    this.resultFunc(index);
  }

  return obj;
}

// Пагинация
export class Pagination {
  constructor(fieldSelector = 'paginatorWrap', param) {
    let {
      dbAction,       // Принудительное Событие запроса
      sortParam = {}, // ссылка на объект отправляемый с функцией запроса
      query,          // функция запроса со страницы
        } = param,
        field = f.qS(fieldSelector);

    if (!(field && param.query)) return;

    this.node           = field;
    this.node.innerHTML = this.template();
    this.node.onclick   = this.onclick.bind(this);

    this.prevBtn = {node: this.node.querySelector('[data-action="prev"]')};
    this.nextBtn = {node: this.node.querySelector('[data-action="next"]')};
    this.onePageBtnWrap = this.node.querySelector('[data-btnwrap]');

    this.query       = query;
    this.dbAction    = dbAction;
    this.sortParam   = sortParam;
    this.activeClass = c.CLASS_NAME.ACTIVE;
  }

  setQueryAction(action) {
    this.dbAction = action;
  }
  setCountPageBtn(count) {
    let pageCount = Math.ceil(+count / this.sortParam.countPerPage );

    if(+this.sortParam.pageCount !== +pageCount) this.sortParam.pageCount = +pageCount;
    else return;

    if (pageCount === 1) {
      f.hide(this.prevBtn.node, this.nextBtn.node);
      this.prevBtn.hidden = true;
      this.nextBtn.hidden = true;
      f.eraseNode(this.onePageBtnWrap);
      return;
    }

    this.fillPagination(pageCount);
  }

  checkBtn() {
    let currPage = +this.sortParam.currPage;
    if (currPage === 0 && !this.prevBtn.hidden) {
      this.prevBtn.hidden = true;
      f.hide(this.prevBtn.node);
    } else if (currPage > 0 && this.prevBtn.hidden) {
      this.prevBtn.hidden = false;
      f.show(this.prevBtn.node);
    }

    let pageCount = this.sortParam.pageCount - 1;
    if (currPage === pageCount && !this.nextBtn.hidden) {
      this.nextBtn.hidden = true;
      f.hide(this.nextBtn.node);
    } else if (currPage < pageCount && this.nextBtn.hidden) {
      this.nextBtn.hidden = false;
      f.show(this.nextBtn.node);
    }

    let n = this.onePageBtnWrap.querySelector('.' + this.activeClass);
    n && n.classList.remove(this.activeClass);
    n = this.onePageBtnWrap.querySelector(`[data-page="${currPage}"]`);
    n && n.parentNode.classList.add(this.activeClass);
  }

  fillPagination(count) {
    let html = '', tpl,
        input = this.templateBtn();

    for(let i = 0; i < count; i++) {
      tpl = input.replace('${page}', i.toString());
      tpl = tpl.replace('${pageValue}', (i + 1).toString());
      html += tpl;
    }

    this.onePageBtnWrap.innerHTML = html;
    this.checkBtn();
  }

  onclick(e) {
    let btn = e && e.target,
        key = btn && btn.dataset.action;
    if (!key) return;

    switch (key) {
      case 'prev':
        this.sortParam.currPage--;
        break;
      case 'next':
        this.sortParam.currPage++;
        break;
      case 'page': this.sortParam.currPage = btn.dataset.page; break;
      case 'count':
        if (this.sortParam.countPerPage === +e.target.value) return;
        this.sortParam.countPerPage = +e.target.value;
        this.sortParam.currPage = 0;
        break;
    }

    //this.l = new LoaderIcon(this.node);
    this.checkBtn();
    this.query(this.dbAction);
  }

  template() {
    return `<div class="pagination justify-content-center">
      <div class="page-item me-2">
        <button type="button" class="page-link" data-action="prev">&laquo;</button>
      </div>
      <div class="page-item pagination" data-btnwrap></div>
      <div class="page-item ms-2">
        <button type="button" class="page-link" data-action="next">&raquo;</button>
      </div>

      <div class="page-item ms-5">
        <select class="form-select d-inline-block" data-action="count">
        <option value="5">5 запись</option>
        <option value="10">10 записей</option>
        <option value="20" selected>20 записей</option>
      </select></div>
    </div>`;
  }

  templateBtn() {
    return `<div class="page-item"><input type="button"
      value="\${pageValue}" class="page-link"
      data-action="page" data-page="\${page}"></div>`;
  }
}

// Сортировка столбцов
export class SortColumns {
  constructor(param) {
    const {
            thead,     // Тег заголовка с кнопками сортировки
            query,     // Функция запроса
            dbAction,  // Событие ДБ
            sortParam, // Объект Параметров
          } = param;

    if (!thead || !query || !sortParam) return;

    let activeClass = c.CLASS_NAME.SORT_BTN_CLASS;
    this.thead = thead;
    this.query = query;
    this.dbAction = dbAction || '';
    this.sortParam = sortParam;
    this.arrow = {
      notActive: '↑↓',
      arrowDown: '↓',
      arrowUp: '↑',
    }

    // Sort Btn
    this.thead.querySelectorAll('input').forEach(n => {
      n.addEventListener('click', e => this.sortRows.call(this, e));
      n.value += ' ' + this.arrow.notActive;

      if (n.dataset.column === this.sortParam.sortColumn) {
        n.classList.add(activeClass);
        n.value = n.value.replace(this.arrow.notActive, this.arrow.arrowDown);
      }

    });
  }

  // сортировка
  sortRows(e) { /*↑↓*/
    let input = e.target,
        colSort = input.dataset.column,
        activeClass = c.CLASS_NAME.SORT_BTN_CLASS,
        {notActive, arrowDown, arrowUp} = this.arrow,
        arrowReg = new RegExp(`${notActive}|${arrowDown}|${arrowUp}`);

    if(this.sortParam.sortColumn === colSort) {
      this.sortParam.sortDirect = !this.sortParam.sortDirect;
    } else {
      this.sortParam.sortColumn = colSort;
      this.sortParam.sortDirect = false;
    }

    let node = this.thead.querySelector(`input.${activeClass}`);
    if (node !== input) {
      node && node.classList.remove(activeClass);
      node && (node.value = node.value.replace(arrowReg, notActive));
      input.classList.add(activeClass);
    }

    if (this.sortParam.sortDirect) input.value = input.value.replace(arrowReg, arrowUp);
    else input.value = input.value.replace(arrowReg, arrowDown);

    this.query(this.dbAction);
  }
}

// Сохранение заказов посетителей
export class SaveVisitorsOrder {
  constructor(createCpNumber) {
    this.nodes = [];
    this.createCpNumber = createCpNumber || this.createCpNumberDefault;
  }

  /**
   * Add nodes to
   * @param collection {nodes[]}
   * @param report {!{cpNumber, inputValue, importantValue, total}}
   * @param event {string}
   *
   * @return result {object} - object contains result work save function;
   */
  setSaveVisitors(collection, report, event = 'click') {
    const result = Object.create(null);
    collection = this.checkNewNodes(collection);
    collection.forEach(n => {
      n.addEventListener(event, async () => {
        await new Promise((res, err) => {
          let i = setInterval(() => this.cpNumber && res(this.cpNumber), 0);
          setTimeout(() => clearInterval(i) && err(''), 1000);
        });
        result.cpNumber = this.cpNumber;
        this.emitAddOrder(report);
      });
    })
    return result;
  }

  createCpNumberDefault() {
    return new Date().getTime().toString().slice(7);
  }

  checkNewNodes(c) {
    if (c instanceof NodeList) c = Object.values(c);
    else if (typeof c !== 'object' || !c.length) c = [c];
    return c.filter(n => !this.nodes.includes(n));
  }

  addOrder(report) {
    console.log('saved');
    typeof report === 'function' && (report = report());

    // Обязательно проверять есть ли вообще что сохранять
    if (!report || !Object.values(report).length) return;

    const data = new FormData();

    data.set('mode', 'DB');
    data.set('dbAction', 'saveVisitorOrder');
    data.set('cpNumber', this.cpNumber);
    data.set('inputValue', JSON.stringify(report['inputValue'] || false));
    data.set('importantValue', JSON.stringify(report['importantValue'] || false));
    data.set('total', report.total);

    q.Post({data});
  }

  async emitAddOrder(report) {
    !this.cpNumber && await new Promise((res, err) => {
      let i = setInterval(() => this.cpNumber && res(this.cpNumber), 0);
      setTimeout(() => clearInterval(i) && err(''), 1000);
    });
    setTimeout(() => this.addOrder(report), 0);
  }

  getCpNumber() {
    !this.cpNumber && (this.cpNumber = this.createCpNumber());
    return this.cpNumber;
  }
}

export class Observer {
  constructor() {
    this.publisher = Object.create(null);
    this.listeners = Object.create(null);
  }
  /**
   * add fixed argument with each fired function
   * @param {string} name
   * @param {object} object - pass object
   */
  addArgument(name, object) {
    this.publisher[name] = object;
  }
  remove(name) {
    delete this.publisher[name];
    delete this.listeners[name];
  }
  getListPublisher() {
    return {
      publisher: Object.keys(this.publisher),
      listeners: Object.keys(this.listeners),
    };
  }
  subscribe(name, func) {
    if (!func) console.warn('Function must have!');
    !this.listeners[name] && (this.listeners[name] = []);
    this.listeners[name].push(func);
    return this.publisher[name] || false;
  }
  fire(name, ...arg) {
    if (Array.isArray(this.listeners[name])) {
      this.listeners[name].forEach(listener => listener(...arg, this.publisher[name]));
      return true;
    }
    return false;
  }
}

// Одноразовые функции
export class OneTimeFunction {
  constructor(funcName, func) {
    this.func = Object.create(null);

    funcName && this.add(funcName, func);
  }

  add(name, func) {
    this.func[name] = func;
  }

  exec(name, ...arg) {
    if (this.func[name]) {
      this.func[name](...arg);
      this.del(name);
    }
  }

  del(name) {
    this.func[name] && (delete this.func[name]);
  }
}
