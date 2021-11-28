'use strict';

export const data = {
  sectionModal: {
    display: false,
    confirmDisabled: true,
    title: '',
  },

  sections       : null,
  sectionTree    : [],
  sectionExpanded: {0: true},
  sectionLoading : false,

  section: {
    id: false,
    parentId: false,
    code: '',
    name: '',
    activity: true,
  },

  sectionSelected: undefined,
  sectionModalSelected: undefined,
  sectionModalSelectedDisabled: false,
  sectionModalLoading: false,
}

export const computed = {
}

export const watch = {
  'section.name'() {
    this.sectionModal.confirmDisabled = !this.section.name;
    if (!this.section.code) {
      this.sectionModalLoading = true;
      setTimeout(() => {
        this.section.code        = this.section.name;
        this.sectionModalLoading = false;
      }, 500);
    }
  },

  'section.parentId'() {
    this.sectionModalSelected = {[this.section.parentId]: true};
  }
}

const reload = that => ({
  dbAction : 'loadSection',
  callback: (fData, aData) => {
    aData['section'] && that.appendSection(aData['section']);

    // Выделить
    that.sectionSelected = {[fData['sectionId']]: true};

    // Открыть секцию если закрыта
    that.sectionExpanded[that.section.parentId] = true;
  },
});

export const methods = {
  loadSections() {
    this.queryParam.dbAction = 'loadSection';
    this.sectionLoading = true;
    this.query().then(data => data['section'] && this.appendSection(data['section']));
  },

  appendSection(sections) {
    let tree = [{
      key: 0,
      label: 'Разделы',
      children: [],
    }];

    const findParentSection = (treeCurr, key) => {
      let result = treeCurr.find(s => s.key === key);
      if (result) return result;

      for (const section of treeCurr) {
        if (!section.children || !section.children.length) continue;
        result = findParentSection(section.children, key);
        if (result) return result;
      }
    }

    //this.oneFunc.exec('msgEmpty', sections);
    if (!sections.length) return;

    sections.forEach(section => {
      const parentSection = findParentSection(tree, section['parentId']);
      !parentSection.children && (parentSection.children = []);
      parentSection.children.push(section);
    });

    this.sections       = sections;
    this.sectionTree    = tree;
    this.sectionLoading = false;
  },

  // Events function
  //--------------------------------------------------------------------------------------------------------------------
  sectionSelectChange(v) {
    this.section.parentId = Object.keys(v)[0];
  },

  openSection() {
    this.queryParam.sectionId = Object.keys(this.sectionSelected)[0];
    this.loadElements();
  },

  createSection() {
    const id = (this.sectionSelected && Object.keys(this.sectionSelected)[0]) || 0;
    this.queryParam.dbAction = 'createSection';

    this.section.name = '';
    this.section.code = '';
    this.section.parentId = id;

    this.sectionModal.title = 'Создать раздел';
    this.sectionModal.confirmDisabled = true;
    this.sectionModal.display = true;

    this.reloadAction = reload(this);
  },

  changeSection() {
    if (!this.sectionSelected) { return; }

    const id = +Object.keys(this.sectionSelected)[0],
          section = Object.values(this.sections).find(s => s.key === id);

    this.queryParam.dbAction = 'changeSection';
    this.queryParam.sectionId = id;

    this.section.name     = section.label;
    this.section.code     = section.code;
    this.section.parentId = section.parentId;

    this.sectionModal.title = 'Редактировать раздел';
    this.sectionModal.display = true;

    this.reloadAction = reload(this);
  },

  deleteSection() {
    if (!this.sectionSelected) { return; }

    this.queryParam.dbAction  = 'deleteSection';
    this.queryParam.sectionId = Object.keys(this.sectionSelected)[0];

    this.sectionModal.title = 'Удалить раздел';
    this.sectionModal.confirmDisabled = false;
    this.sectionModal.display = true;

    this.reloadAction = {
      dbAction : 'loadSection',
      callback: (d, data) => this.appendSection(data['section']),
    };
  },

  sectionConfirm() {
    this.sectionLoading = true;
    this.queryParam = Object.assign(this.queryParam, this.section);
    this.query();
    this.sectionModal.display = false;
  },

  sectionCancel() {
    this.sectionModal.display = false;
  }
}
