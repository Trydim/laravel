'use strict';

export class Main {
  constructor(type, props = {}) {
    this.M = f.initModal();
    this.delayFunc = () => {};

    this.reloadAction = false;
    this.type         = type;
    this.node         = Object.create(null);
    this.tmp          = Object.create(null);

    this.setQueryParam();
    this.setData(props.db);
    this.onMainEvent();
  }

  setQueryParam() {


    Object.defineProperty(this.queryParam, 'form', {
      enumerable: false,
      writable: true,
    });
  }
  setData(db) {
    let node = f.qS(`#${this.type}Column`);
    node && (this.setting = node.value.split(','));

    typeof db === 'object' && (this.db = Object.create(db));
  }

  // Events function
  //--------------------------------------------------------------------------------------------------------------------

  commonEvent(e) {
    let target = e.target,
        action = target.dataset.action;

    if (action === 'confirmYes') {
      this.delayFunc();
      this.delayFunc = () => {};
      this.query();
    } else if (action === 'confirmNo') {
      this.reloadAction = false;
    } else {
      this.queryParam.dbAction = action;
      this[action] && this[action](target);
    }
  }

  // Двойной клик (изменить)
  dblClick(e) {
    let id = e.target.closest('tr').querySelector('[data-id]').dataset.id,
        module = this['id'],
        selectedIds = module.getSelected();

    module.clear();
    module.add(id);
    e.target.dataset.action = this.type === 'elements' ? 'changeElements' : 'changeOptions';
    this.commonEvent(e);

    module.clear();
    module.add(selectedIds);
    module.checkedRows();
    delete e.target.dataset.action;
  }

  // Bind events
  //--------------------------------------------------------------------------------------------------------------------

  /**
   * @param node
   * @param func
   * @param options
   * @param eventType
   */
  onEventNode(node, func, options = {}, eventType = 'click') {
    node.addEventListener(eventType, (e) => func.call(this, e), options);
  }

  onMainEvent() {
    [this.M.btnConfirm, this.M.btnCancel].forEach(n => {
      n.addEventListener('click', e => this.commonEvent(e));
    });
  }
}

// Elements+Sections
export class Common extends Catalog {
  constructor(type, props) {
    super(type, props);
    this.setCommonParam();
  }

  getLang(name) {
    return this.db.lang[this.type][name] || name;
  }

  setCommonParam() {
    this.itemList = new Map();

    this.sortParam = {
      sortDirect: true, // true = DESC, false
      currPage: 0,
      countPerPage: 20,
      pageCount: 0,
    };

    this.oneFunc = new f.OneTimeFunction('setTablesHeaders',
      () => this.setTablesHeaders()
    );
  }
  setNodes(field, tmp) {
    this.node.field        = field;
    this.node.tableWrap    = field.querySelector('[data-field="tableWrap"]');
    this.node.fieldT       = field.querySelector('table');
    this.node.fieldTBody   = field.querySelector('tbody');
    this.node.btnWrap      = field.querySelector('[data-field="btnWrap"]');
    this.node.selectedList = field.querySelector('[data-field="selectedList"]');

    this.tmp = Object.create(tmp);
    this.tmp.form = f.gTNode(`#${this.type}Form`);
  }
  setItemsList(data) {
    this.itemList = new Map();
    data.forEach(i => this.itemList.set(i.id, i));
  }
  setTablesHeaders() {
    let html = '<tr><th></th>',
        node = this.node.field.querySelector('thead');

    this.setting.map(column => {
      html += f.replaceTemplate(this.tmp.tHead, {name: this.getLang(column), column});
    });

    node.innerHTML = html + '</tr>';

    new f.SortColumns({
      thead: node,
      query: action => this.query(action).then(data => f.observer.fire('sortEvent', data)),
      dbAction: this.type === 'elements' ? 'openSection' : 'openElement',
      sortParam: this.sortParam,
    });
  }
  setImage(i) {
    if (!Array.isArray(i)) return [];
    return i.reduce((r, i) => {
      r.push({
        src   : i.path,
        name  : i.name,
        format: i.format,
      });
      return r;
    }, []);
  }
  showTablesItems(data) {
    this.oneFunc.exec('setTablesHeaders');

    const trNode = [],
          colSelect = {
            'activity': v => !!+v ? '<td>+</td>' : '<td>-</td>',

            'ex': v => '<td></td>',
            'images': v => {
              const td = this.tmp.imgCell.cloneNode(true);
              td.innerHTML = f.replaceTemplate(this.tmp.img, this.setImage(v));
              return td.outerHTML;
            },

            'unitId': v => `<td>${this.db.units[v]['shortName']}</td>`,
            'moneyInputId': v => `<td>${this.db.money[v].code}</td>`,
            'moneyOutputId': v => `<td>${this.db.money[v].code}</td>`,
          };

    data.map(row => {
      let tr = document.createElement('tr');
      tr.innerHTML = f.replaceTemplate(this.tmp.checkbox, {id: row.id});
      this.setting.forEach(col => {
        tr.innerHTML += (colSelect[col] && colSelect[col](row[col])) || `<td>${row[col]}</td>`;
      });
      trNode.push(tr);
    });

    f.eraseNode(this.node.fieldTBody).append(...trNode);
  }
  prepareItems(data) {
    this.setItemsList(data);
    if (data.length) {
      this.showTablesItems(data);
      f.show(this.node.tableWrap, this.node.btnWrap);
    } else {
      f.showMsg('Раздел пустой');
      f.hide(this.node.tableWrap, this.node.btnWrap);
    }
  }

  selectedRow(a, size, obj) {
    if (this.node.fieldT !== obj.table) return;

    const func = item => {
      return `<div style="cursor: pointer" data-id="${item.id}" data-action="removeSelected">${item.name}</div>`;
    }

    this.node.selectedList.innerHTML = a.map(id => this.itemList.get(+id)).map(func).join('')
                                       || '<div>Ничего не выбрано!</div>';
  }
}
