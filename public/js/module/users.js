var __webpack_exports__ = {};
/*!**********************************!*\
  !*** ./js/module/users/users.js ***!
  \**********************************/


const users = {
  form: new FormData(),

  needReload: false,
  table: f.qS('#usersTable'),
  tbody: '',
  impValue: '',
  confirm: f.qS('#confirmField'),
  confirmMsg: false,

  tmp: {
    form: f.gTNode('#userForm'),
  },

  queryParam: {
    dbAction    : 'loadUsers',
    sortColumn  : 'created_at',
    sortDirect  : false, // true = DESC, false
    currPage    : 0,
    countPerPage: 20,
    pageCount   : 0,
  },

  delayFunc: () => {},
  //statusList: Object.create(null), // Типы доступов

  usersList: new Map(),

  init() {
    this.p = new f.Pagination('#paginator', {
      dbAction : 'loadUsers',
      sortParam: this.queryParam,
      query    : this.query.bind(this),
    });
    this.id = new f.SelectedRow({table: this.table});
    new f.SortColumns({
      thead: this.table.querySelector('thead'),
      query: this.query.bind(this),
      dbAction : 'loadUsers',
      sortParam: this.queryParam,
    });
    this.M = f.initModal();
    this.query();

    this.onEvent();
  },

  setUsers(data) {
    this.usersList = new Map();
    data.forEach(i => this.usersList.set(i.id, i));
  },
  fillTable(data) {
    this.contValue || (this.contValue = f.gT('#tableContactsValue'));
    data = data.map(item => {
      item.name = _(item.name);
      if (item.hasOwnProperty('activity')) {
        item.activityValue = !!item.activity;
        item.activity = item.activityValue ? '+' : '-';
      }

      if(true /* TODO настройки вывода даты*/) {
        for (let i in item) {
          if(i.includes('date')) {
            item[i] = item[i].replace(/ |(\d\d:\d\d:\d\d)/g, '');
          }
        }
      }

      return item;
    })

    let html  = '';
    this.tbody || (this.tbody = this.table.querySelector('tbody tr').outerHTML);
    html += f.replaceTemplate(this.tbody, data);
    this.table.querySelector('tbody').innerHTML = html;
  },

  setPermission(data) {
    this.permissionList = new Map();
    data.forEach(i => this.permissionList.set(i['ID'], i));
  },
  // Заполнить статусы
  fillPermission(data) {
    let tmp = f.gT('#permission'), html  = '';

    html += f.replaceTemplate(tmp, data);

    f.gI('selectPermission').innerHTML = html;
  },

  query() {
    Object.entries(this.queryParam).map(param => {
      this.form.set(param[0], param[1]);
    })

    f.Post({data: this.form}).then(data => {

      if(this.needReload) {
        this.needReload = false;
        this.selectedId = new Set();
        this.queryParam.dbAction = 'loadUsers';
        this.queryParam.usersId = '[]';
        this.query();
        return;
      } else {
        this.confirmMsg && f.showMsg(this.confirmMsg, data.status) && (this.confirmMsg = false);
      }

      if(data['permissionUsers']) { this.setPermission(data['permissionUsers']); this.fillPermission(data['permissionUsers']); }
      if(data['users']) { this.setUsers(data['users']); this.fillTable(data['users']); }
      if(data['countRows']) this.p.setCountPageBtn(data['countRows']);
    });
  },

  // Events function
  //--------------------------------------------------------------------------------------------------------------------

  // кнопки открыть закрыть и т.д.
  actionBtn(e) {
    let target = e.target,
        action = target.getAttribute('data-action'),
        form;

    if (['addUser', 'changeUser'].includes(action)) {
      this.delayFunc = () => {
        const f = new FormData(form), res = {};
        for (const [k, v] of f.entries()) {
          res[k] = v;
        }
        this.queryParam.authForm = JSON.stringify(res);
      };
    }

    let select = {
      'addUser': () => {
        form = this.tmp.form.cloneNode(true);
        form.querySelector('#changeField').remove();
        this.confirmMsg = 'Новый пользователь добавлен';
        this.M.show('Добавление пользователя', form);
        f.maskInit(form.querySelector('[name="phone"]'));
      },
      'changeUser': () => {
        if (!this.id.getSelectedSize()) { f.showMsg('Выберите минимум 1 пользователя', 'error'); return; }

        let oneElements = this.id.getSelectedSize() === 1, node,
            id = this.id.getSelected(),
            users = this.usersList.get(+id[0]);
        form = this.tmp.form.cloneNode(true);

        this.queryParam.usersId = JSON.stringify(id);

        node = form.querySelector('[name="name"]');
        if (oneElements) node.value = users.name;
        else node.parentNode.remove();

        node = form.querySelector('[name="permissionId"]');
        if (oneElements) node.value = users['permissionId'];
        else node.value = 1;

        node = form.querySelector('[name="login"]');
        if (oneElements) node.value = users['login'];
        else node.parentNode.remove();

        form.querySelector('[name="password"]').parentNode.remove();

        node = form.querySelector('[name="phone"]');
        if (oneElements) node.value = users['phone'];
        else node.parentNode.remove();

        node = form.querySelector('[name="email"]');
        if (oneElements) node.value = users['email'];
        else node.parentNode.remove();

        node = form.querySelectorAll('.managerField');
        if (oneElements) {
          node.forEach(n => {
            let input = n.querySelector('input[name], textarea[name]'),
                name = input.name;

            users[name] && (input.value = users[name]);
          });
        } else node.forEach(n => n.remove());

        node = form.querySelector('[name="activity"]');
        node.checked = oneElements ? users.activityValue : true;

        this.confirmMsg = 'Изменения сохранены';
        this.M.show('Изменение пользователей', form);
      },
      'changeUserPassword': () => { // TODO доработать изменение пароля
        if (this.id.getSelectedSize() !== 1) { f.showMsg('Выберите только одного пользователя', 'error'); return; }

        let id   = this.id.getSelected(),
            user = this.usersList.get(+id[0]),
            form = f.gTNode('#userChangePassForm');

        this.queryParam.usersId = JSON.stringify(id);

        let newPass = form.querySelector('[name="newPass"]'),
            repeatPass = form.querySelector('[name="repeatPass"]');

        this.onEventNode(newPass, e => this.changeTextInput(e, repeatPass), {}, 'change');
        this.onEventNode(repeatPass, e => this.changePassword(e, newPass), {}, 'change');

        this.confirmMsg = 'Новый пароль сохранен';
        this.M.show('Изменить пароль пользователя ' + user.name, form);
      },
      'delUser': () => {
        if (!this.id.getSelectedSize()) return;

        this.queryParam.usersId = JSON.stringify(this.id.getSelected());
        this.delayFunc = () => this.id.clear();

        this.confirmMsg = 'Удаление успешно';
        this.M.show('Удалить', 'Удалить выбранных пользователя?');
      },
    }

    if (action === 'confirmYes') { // Закрыть подтверждением
      this.delayFunc();
      this.delayFunc = () => {};
      this.needReload = {dbAction: 'loadUsers'};
      this.query();
    } else if (action === 'confirmNo') {
      this.reloadAction = false;
    } else {
      this.queryParam.dbAction = action;
      select[action] && select[action]();
    }
  },

  changeTextInput(e, repeatPass) {
    if (e.target.value.length <= 2) e.target.value = '';
    repeatPass.value = '';
  },
  changeCheckInput(e) {
    this.queryParam[e.target.name] = e.target.checked;
  },
  changePassword(e, newPass) {
    if (e.target.value !== newPass.value) {
      e.target.value = newPass.value = '';
      f.showMsg('Пароли не совпадают', 'error');
      return;
    }
    this.queryParam['validPass'] = e.target.value;
  },

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
  },

  onEvent() {
    // Action buttons
    f.qA('input[data-action]', 'click', (() => (e) => this.actionBtn.call(this, e))());
  },
}

users.init();


//# sourceMappingURL=users.js.map