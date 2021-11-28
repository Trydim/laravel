'use strict';

export const data = {
  optionsModal: {
    display: false,
    confirmDisabled: true,
    title: '',
    single: true,
  },

  options: [],
  optionsLoading: false,

  option: {
    id           : 0,
    type         : 0,
    elementId    : 0,
    images       : [],
    name         : '',
    unitId       : 0,
    activity     : true,
    sort         : 100,
    percent      : 0,
    moneyInputId : 0,
    inputPrice   : 0,
    moneyOutputId: 0,
    outputPrice  : 0,
    property     : {},
  },

  fieldChange: {
    unitId       : true,
    moneyInputId : true,
    percent      : true,
    moneyOutputId: true,
    activity     : true,
    sort         : true,
    property     : false,
  },

  elementLoaded  : 0,
  optionsSelected: [],

  optionsSelectedShow: false,
  elementParentModalDisabled: false,
  elementParentModalSelected: undefined,

  optionsColumns: [
    {name: 'Номер', value: 'id'},
    {name: 'Файлы', value: 'images'},
    {name: 'Ед.Измерения', value: 'unitName'},
    {name: 'activity', value: 'activity'},
    {name: 'sort', value: 'Сортировка'},
    {name: 'moneyInputName', value: 'moneyInputName'},
    {name: 'inputPrice', value: 'inputPrice'},
    {name: 'outputPercent', value: 'outputPercent'},
    {name: 'moneyOutputName', value: 'moneyOutputName'},
    {name: 'outputPrice', value: 'outputPrice'},
  ],

  optionsColumnsSelected: undefined,
  files: {},
}

export const watch = {
  'option.name'() {
    this.optionsModal.confirmDisabled = !this.option.name;
  },
}

export const computed = {
  optionsColumnsValues() {
    return this.optionsColumnsSelected.map(v => v.value);
  },

  getOptionSelectedId() {
    return this.optionsSelected.map(i => i.id);
  },
}

const reload = that => ({
  dbAction : 'openElement',
  callback: (fData, aData) => {
    that.options         = aData['options']
    that.optionsLoading  = false;
    that.optionsSelected = [];
    that.files           = Object.create(null);
  }
});

export const methods = {
  loadOptions(id) {
    this.queryParam.dbAction = 'openElement';
    this.queryParam.elementsId = id;
    this.elementLoaded = id;
    this.optionsLoading = true;
    this.query().then(data => {
      this.options        = data['options'];
      this.optionsLoading = false;
    })
  },

  enableOptionField() {
    this.fieldChange = {
      unitId       : true,
      moneyInputId : true,
      percent      : true,
      moneyOutputId: true,
      activity     : true,
      sort         : true,
      property     : false,
    };
  },

  setOptionModal(title, confirmDisabled, single) {
    single && this.enableOptionField();
    this.optionsModal = {display: true, confirmDisabled, title, single};
  },
  clearFiles(node) {
    const input = document.createElement('input');
    input.type = 'file';
    node.files = input.files;
  },
  setImages(option) {
    this.option.images = option.images.map(f => f.id).join(',');
    option.images.forEach(f => {
      this.files['F_' + f.id] = f;
      this.queryFiles['F_' + f.id] = f.id;
    });
  },

  // Events function
  //--------------------------------------------------------------------------------------------------------------------
  checkColumn(v) {
    return this.optionsColumnsValues.includes(v);
  },

  selectedAllOptions() {
    this.optionsSelected = Object.values(this.options);
  },
  clearAllOptions() {
    this.optionsSelected = [];
  },
  unselectedOption(id) {
    this.optionsSelected = this.optionsSelected.filter(i => i.id !== id);
    this.optionsSelectedShow = !!this.optionsSelected.length;
  },

  createOption() {
    this.queryParam.dbAction = 'createOption';

    this.option.name = '';
    this.option.unitId = this.units[0].id;
    this.option.moneyInputId  = this.money[0].id;
    this.option.moneyOutputId = this.money[0].id;
    this.option.sort = 100;

    this.setOptionModal('Создать', true, true);
    this.reloadAction = reload(this);
  },
  changeOptions() {
    if (!this.optionsSelected.length) { return; }
    const el = this.optionsSelected[0],
          single = this.optionsSelected.length === 1;

    this.queryParam.optionsId = JSON.stringify(this.getOptionSelectedId);
    this.queryParam.dbAction = 'changeOptions';


    this.option.name      = single ? el.name : '';
    this.option.elementId = this.elementLoaded;
    this.option.unitId    = single ? el.unitId : this.units[0].id;
    this.option.moneyInputId  = single ? el.moneyInputId : this.money[0].id;
    this.option.moneyOutputId = single ? el.moneyOutputId : this.money[0].id;
    this.option.activity = single ? !!el.activity : true;
    this.option.sort     = this.getAvgSort(this.optionsSelected);

    single && this.setImages(el);

    this.setOptionModal('Редактировать', false, single);
    this.reloadAction = reload(this);
  },
  copyOption() {
    if (this.optionsSelected.length !== 1) { return; }
    const el = this.optionsSelected[0];

    this.queryParam.optionsId = JSON.stringify(this.getOptionSelectedId);
    this.queryParam.dbAction = 'copyOption';

    this.option.name      = el.name;
    this.option.elementId = this.elementLoaded;
    this.option.unitId    = el.unitId;
    this.option.moneyInputId  = el.moneyInputId;
    this.option.moneyOutputId = el.moneyOutputId;
    this.option.activity = !!el.activity;
    this.option.sort     = el.sort;
    this.setImages(el);

    this.setOptionModal('Редактировать', false, true);
    this.reloadAction = reload(this);
  },
  deleteOptions() {
    if (!this.optionsSelected.length) { return; }

    this.queryParam.elementsId = JSON.stringify(this.getOptionSelectedId);
    this.queryParam.dbAction   = 'deleteOptions';

    this.setElementModal('Удалить', false, false);
    this.reloadAction = reload(this);
  },

  addFile(e) {
    Object.values(e.target.files).forEach(file => {
      let id    = Math.random() * 10000 | 0,
          error = false;

      file.fileError = file.size > 1024*1024;
      if (file.fileError && !error) error = true;

      this.queryFiles.id && (id += '1');
      this.queryFiles[id] = file;
      this.files[id] = {
        name: file.name,
        src: URL.createObjectURL(file),
        error,
      };
    });
    this.clearFiles(e.target);
  },
  removeFile(e) {
    const id = e.target.closest('[data-id]').dataset.id;
    delete this.queryFiles[id];
    delete this.files[id];
  },

  optionsConfirm() {
    //this.optionsLoading = true;
    this.queryParam = Object.assign(this.queryParam, this.option, {fieldChange: JSON.stringify(this.fieldChange)});
    this.query();
    this.optionsModal.display = false;
  },
  optionsCancel() {
    this.files = Object.create(null);
    this.optionsModal.display = false;
  },
}
