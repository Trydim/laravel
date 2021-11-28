'use strict';

export const data = {
  permissionsChanged: false,
  permissionsData: [],
  permissionsMenu: [],

  permission: {
    id: 0,
    name: '',
    current: {
      id: 0,
      name: '',
      accessVal: {
        menu: '',
      },
    },
    menu: [[], []],
  },
}

export const watch = {
  'permission.id'() {
    if (!this.permission.id) return;
    const menu = this.permission.menu,
          sPer = this.permissionsData.find(i => i.id === this.permission.id),
          accessVal = sPer['accessVal']['menu'] || '';

    menu[0] = this.permissionsMenu.filter(m => !accessVal.includes(m.id));
    menu[1] = this.permissionsMenu.filter(m => accessVal.includes(m.id));

    this.permission.current = sPer;
  },
}

export const computed = {}

export const methods = {
  addPermission() {
    const id = 'new_' + Math.random();

    this.permissionsData.push({
      id,
      name: this.permission.name,
      accessVal: {},
    });

    this.permission.name = '';
    this.permission.id = id;
  },
  removePermission() {
    this.permissionsData = this.permissionsData.filter(i => i.id !== this.permission.id);
    this.permission.id = this.permissionsData.length ? this.permissionsData[0].id : 0;
  },

  pickedChange() {
    this.permissionsChanged = true;
    this.permission.current.accessVal.menu = this.permission.menu[1].map(m => m.id).join(',');
  }
}
