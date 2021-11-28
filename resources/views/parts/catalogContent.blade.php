<div id="catalogForm">
  <div class="container-fluid">
    <div class="row">
      <div id="searchField" class="position-relative form-group col-12 pt-2">
        <span v-if="searchShow" class="p-float-label">
          <p-input-text v-model="search" :class="'w-100'"></p-input-text>
          <label>Поиск</label>
        </span>
        <p-button v-tooltip.left="'Скрыть'" :label="searchShow ? '-' : '+'" :class="'position-absolute p-0'"
                  style="width: 30px; height: 20px; right: 12px; top: 8px;"
                  @click="searchShow = !searchShow"
        ></p-button>
      </div>
    </div>
    <hr>
    <div class="row">

      <div class="col-3 overflow-auto bg-style-sheet position-relative"
           :style="{'max-width': sectionShow ? '25%' : '20px'}"
           :class="{'p-0': !sectionShow}"
      >
        <p-button v-tooltip.bottom="'Скрыть'" :label="sectionShow ? '<' : '>'" :class="'position-absolute p-0 p-button-white'"
                  style="width: 20px; right: 0; top: 0; bottom: 0;"
                  @click="sectionShow = !sectionShow"
        ></p-button>

        <template v-if="sectionShow">
          <Tree :value="sectionTree"
                :loading="sectionLoading"
                :expanded-keys="sectionExpanded"
                selection-mode="single"
                v-model:selection-keys="sectionSelected"
                @dblclick="openSection"
          ></Tree>

          <div class="d-flex mt-2 justify-content-center">
            <p-button v-tooltip.bottom="'Создать раздел'" icon="pi pi-plus-circle" class="p-button-success"
                      :loading="sectionLoading" @click="createSection"></p-button>
            <p-button v-tooltip.bottom="'Изменить раздел'" icon="pi pi-cog" class="p-button-warning mx-2"
                      :loading="sectionLoading" @click="changeSection"></p-button>
            <p-button v-tooltip.bottom="'Удалить раздел'" icon="pi pi-trash" class="p-button-danger"
                      :loading="sectionLoading" @click="deleteSection"></p-button>
          </div>

          <p-dialog v-model:visible="sectionModal.display">
            <template #header>
              <h4>@{{ sectionModal.title }}</h4>
            </template>

            <div v-if="queryParam.dbAction !== 'deleteSection'">
              <div class="row align-items-center my-2">
                <div class="col">Имя раздела:</div>
                <div class="col">
                  <p-input-text class="w-100" v-model="section.name" autofocus></p-input-text>
                </div>
              </div>
              <div class="row align-items-center my-2">
                <div class="col">Символьный код раздела:</div>
                <div class="col">
                  <span class="p-input-icon-right">
                    <i v-if="sectionModalLoading" class="pi pi-spin pi-spinner"></i>
                    <p-input-text class="w-100" v-model="section.code"></p-input-text>
                  </span>
                </div>
              </div>
              <div class="row align-items-center">
                <div class="col">Родительский раздел:</div>
                <div class="col">
                  <p-tree-select class="w-100" selectionMode="single"
                                 :options="sectionTree"
                                 :disabled="sectionModalSelectedDisabled"
                                 v-model="sectionModalSelected"
                                 @change="sectionSelectChange($event)">
                  </p-tree-select>
                </div>
              </div>
              <div class="row my-2 py-2">
                <div class="col">Доступен:</div>
                <div class="col text-center"><p-checkbox :binary="true" v-model="section.activity"></p-checkbox></div>
              </div>
            </div>
            <div v-else>
              Удалить раздел (включая все элементы)
            </div>

            <template #footer>
              <p-button label="Yes" icon="pi pi-check" :disabled="sectionModal.confirmDisabled" @click="sectionConfirm()"></p-button>
              <p-button label="No" icon="pi pi-times" class="p-button-text" @click="sectionCancel()"></p-button>
            </template>
          </p-dialog>
        </template>

      </div>

      <!-- Elements -->
      <div class="col">
        <div class="position-relative"
             :style="{'max-height': elementShow ? '100%' : '30px'}"
        >
          <p-button v-tooltip.left="'Скрыть'" :label="elementShow ? '-' : '+'" :class="'position-absolute p-0 p-button-white'"
                    style="width: 30px; height: 20px; right: 1px; top: 1px;"
                    :style="{'z-index': 100}"
                    @click="elementShow = !elementShow"
          ></p-button>

          <template v-if="elementShow">
            <p-table v-if="elements.length"
                     :value="elements" datakey="id"
                     :loading="elementsLoading"
                     :resizable-columns="true" column-resize-mode="fit" show-gridlines
                     selection-mode="multiple" :meta-key-selection="false"
                     :paginator="elements.length > 5" :rows="10" :rows-per-page-options="[5,10,20,50]"
                     paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                     current-page-report-template="Showing {first} to {last} of {totalRecords}"
                     responsive-layout="scroll"
                     v-model:selection="elementsSelected"
                     @dblclick="loadElement($event)"
            >
              <p-t-column field="id" :sortable="true" header="{{ __('db.elements.id') }}">
                <template #body="slotProps">
                  <div :data-id="slotProps.data.id">
                    @{{ slotProps.data.id }}
                  </div>
                </template>
              </p-t-column>
              <p-t-column field="codeName" :sortable="true" header="{{ __('db.elements.code') }}"></p-t-column>
              <p-t-column field="name" :sortable="true" header="{{ __('db.elements.name') }}"></p-t-column>
              <p-t-column field="activity" :sortable="true" header="{{ __('db.elements.activity') }}" :class="'text-center'">
                <template #body="slotProps">
                  <span v-if="!!slotProps.data.activity" class="pi pi-check" style="color: green"></span>
                  <span v-else class="pi pi-times" style="color: red"></span>
                </template>
              </p-t-column>
              <p-t-column field="sort" :sortable="true" header="{{ __('db.elements.sort') }}" :class="'text-center'"></p-t-column>
              <p-t-column field="lastEditDate" :sortable="true" header="{{ __('db.elements.lastEditDate') }}"></p-t-column>
            </p-table>

            <div class="mt-1 d-flex justify-content-between">
              <span>
                <p-button v-tooltip.bottom="'Создать элемент'" icon="pi pi-plus-circle" class="p-button-success me-2"
                          :loading="elementsLoading" @click="createElement"></p-button>
                <span v-if="elements.length">
                  <p-button v-tooltip.bottom="'Изменить элемент'" icon="pi pi-cog" class="p-button-warning me-2"
                            :loading="elementsLoading" @click="changeElements"></p-button>
                  <p-button v-tooltip.bottom="'Копировать элемент'" icon="pi pi-copy" class="p-button-warning me-2"
                            :loading="elementsLoading" @click="copyElement"></p-button>
                  <p-button v-tooltip.bottom="'Удалить элемент'" icon="pi pi-trash" class="p-button-danger me-2"
                            :loading="elementsLoading" @click="deleteElements"></p-button>
                </span>
              </span>

              <div v-if="elements.length">
                <p-button v-tooltip.bottom="'Выделить все'" icon="pi pi-check" class="p-button-warning"
                          :loading="elementsLoading" @click="selectedAll"></p-button>
                <p-button v-tooltip.left="'Снять выделение'" icon="pi pi-times" class="p-button-danger ms-2"
                          :loading="elementsLoading" @click="clearAll"></p-button>
                <p-button v-if="elementsSelected.length"
                          v-tooltip.left="'Показать выбранные'" icon="pi pi-bars" class="p-button-warning ms-2"
                          @click="elementsSelectedShow = !elementsSelectedShow"
                ></p-button>
              </div>
            </div>

            <div v-if="elementsSelectedShow" class="position-absolute bg-white p-2 end-0 bottom-0" style="min-width: 230px">
              <div class="position-relative pt-4">
                <p-button v-tooltip.left="'Закрыть'" icon="pi pi-times" class="position-absolute top-0 end-0"
                          style="width: 25px;height: 25px"
                          @click="elementsSelectedShow = !elementsSelectedShow"></p-button>
                <div v-for="item of elementsSelected" class="d-flex justify-content-between align-items-center my-1">
                  <div>@{{ item.name }}</div>
                  <p-button v-tooltip.left="'Убрать выделение'" icon="pi pi-times" class="p-button-danger p-button-outlined"
                            style="width: 25px;height: 25px"
                            @click="unselectedElement(item.id)"></p-button>
                </div>
              </div>
            </div>

            <p-dialog v-model:visible="elementsModal.display" :modal="true">
              <template #header>
                <h4>@{{ elementsModal.title }}</h4>
              </template>

              <div v-if="queryParam.dbAction !== 'deleteElements'" style="min-width: 500px">
                <!-- Тип элемента -->
                <div class="row align-items-center my-1">
                  <div class="col d-flex justify-content-between align-content-center">
                    Тип элемента:
                    <p-toggle-button v-if="!elementsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                                     v-tooltip.bottom="'Не редактировать'"
                                     v-model="fieldChange.type"
                    ></p-toggle-button>
                  </div>
                  <div class="col">
                    <p-select class="w-100" option-label="name" option-value="symbolCode"
                              :editable="true"
                              :disabled="!fieldChange.type"
                              :options="codes"
                              v-model="element.type">
                    </p-select>
                  </div>
                </div>
                <!-- Имя элемента -->
                <div v-if="elementsModal.single" class="row align-items-center mb-1">
                  <div class="col d-flex" style="justify-content: flex-start">Имя элемента:</div>
                  <div class="col">
                    <p-input-text class="w-100" v-model="element.name" @input="elementNameInput()" autofocus></p-input-text>
                  </div>
                </div>
                <!-- Раздел -->
                <div class="row align-items-center mb-1">
                  <div class="col d-flex justify-content-between align-content-center">
                    <span>
                      Родительский раздел <i class="pi pi-question" v-tooltip.bottom="'Выберите раздел!'" style="color: red"></i>:
                    </span>
                    <p-toggle-button v-if="!elementsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                                     v-tooltip.bottom="'Не редактировать'"
                                     v-model="fieldChange.parentId"
                    ></p-toggle-button>
                  </div>
                  <div class="col">
                    <p-tree-select class="w-100" selectionMode="single"
                                   :options="sectionTreeModal"
                                   :disabled="elementParentModalDisabled || !fieldChange.parentId"
                                   v-model="elementParentModalSelected"
                                   @change="elementParentModalSelectedChange($event)">
                    </p-tree-select>
                  </div>
                </div>
                <!-- Доступен -->
                <div class="row align-items-center mb-1">
                  <div class="col d-flex justify-content-between align-content-center">
                    Доступ:
                    <p-toggle-button v-if="!elementsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                                     v-tooltip.bottom="'Не редактировать'"
                                     v-model="fieldChange.activity"
                    ></p-toggle-button>
                  </div>
                  <div class="col text-center">
                    <p-toggle-button on-icon="pi pi-check" off-icon="pi pi-times" class="w-100"
                                     on-label="Активен" off-label="Неактивен"
                                     :disabled="!fieldChange.activity"
                                     v-model="element.activity"
                    ></p-toggle-button>
                  </div>
                </div>
                <!-- Сортировка -->
                <div class="row align-items-center my-1">
                  <div class="col d-flex justify-content-between align-content-center">
                    Сортировка:
                    <p-toggle-button v-if="!elementsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                                     v-tooltip.bottom="'Не редактировать'"
                                     v-model="fieldChange.sort"
                    ></p-toggle-button>
                  </div>
                  <div class="col">
                    <p-input-number class="w-100" :disabled="!fieldChange.sort"
                                    v-model="element.sort"
                                    :min="0"
                    ></p-input-number>
                  </div>
                </div>
              </div>
              <div v-else>
                Удалить элемент(ы)
              </div>

              <template #footer>
                <p-button label="Yes" icon="pi pi-check" :disabled="elementsModal.confirmDisabled" @click="elementConfirm()"></p-button>
                <p-button label="No" icon="pi pi-times" class="p-button-text" @click="elementCancel()"></p-button>
              </template>
            </p-dialog>
          </template>
        </div>
      </div>

    </div>
  </div>
  <hr>

  <!-- Options -->
  <div class="container-fluid position-relative">

    <p-table v-if="options.length"
             :value="options" datakey="id"
             :loading="optionsLoading"
             :resizable-columns="true" column-resize-mode="fit" show-gridlines
             selection-mode="multiple" :meta-key-selection="false"
             :scrollable="true"
             responsive-layout="scroll"
             v-model:selection="optionsSelected"
             @dblclick="changeOptions($event)"
             :bodyClass="'text-center'"
    >
      <template #header>
        <div style="text-align:left">
          <p-multi-select :model-value="optionsColumnsSelected"
                          :options="optionsColumns"
                          option-label="name"
                          @update:model-value="onToggle"
                          placeholder="Настроить колонки" style="width: 20em"
          ></p-multi-select>
        </div>
      </template>
      <p-t-column v-if="checkColumn('id')" field="id" :sortable="true" header="{{ __('db.options.id') }}" :class="'text-center'">
        <template #body="slotProps">
          <span :data-id="slotProps.data.id">@{{ slotProps.data.id }}</span>
        </template>
      </p-t-column>
      <p-t-column v-if="checkColumn('images')" field="images" header="{{ __('db.options.images') }}">
        <template #body="slotProps">
          <p-image v-for="images of slotProps.data.images"
                   :src="images.src" preview
                   image-style="max-width: 50px"
          ></p-image>
        </template>
      </p-t-column>
      <p-t-column field="name" :sortable="true" header="{{ __('db.options.name') }}"></p-t-column>
      <p-t-column v-if="checkColumn('unitName')" field="unitName" header="{{ __('db.options.unitName') }}"></p-t-column>
      <p-t-column v-if="checkColumn('activity')" field="activity" :sortable="true" header="{{ __('db.options.activity') }}" :class="'text-center'">
        <template #body="slotProps">
          <span v-if="!!slotProps.data.activity" class="pi pi-check" style="color: green"></span>
          <span v-else class="pi pi-times" style="color: red"></span>
        </template>
      </p-t-column>
      <p-t-column v-if="checkColumn('sort')" field="sort" :sortable="true" header="{{ __('db.options.sort') }}" :class="'text-center'"></p-t-column>
      <p-t-column v-if="checkColumn('moneyInputName')" field="moneyInputName" :sortable="true" header="{{ __('db.options.moneyInputName') }}"></p-t-column>
      <p-t-column v-if="checkColumn('inputPrice')" field="inputPrice" :sortable="true" header="{{ __('db.options.inputPrice') }}">
        <template #body="slotProps">
          @{{ (+slotProps.data.inputPrice).toFixed(2) }}
        </template>
      </p-t-column>
      <p-t-column v-if="checkColumn('outputPercent')" field="outputPercent" :sortable="true" header="{{ __('db.options.outputPercent') }}"></p-t-column>
      <p-t-column v-if="checkColumn('moneyOutputName')" field="moneyOutputName" :sortable="true" header="{{ __('db.options.moneyOutputName') }}"></p-t-column>
      <p-t-column v-if="checkColumn('outputPrice')" field="outputPrice" :sortable="true" header="{{ __('db.options.outputPrice') }}"></p-t-column>
    </p-table>

    <div class="mt-1 d-flex justify-content-between">
      <div></div>
      <div>
        <p-button v-tooltip.bottom="'Добавить вариант'" icon="pi pi-plus-circle" class="p-button-success me-2"
                  :loading="optionsLoading" @click="createOption"></p-button>

        <span v-if="options.length">
          <p-button v-tooltip.bottom="'Изменить вариант'" icon="pi pi-cog" class="p-button-warning me-2"
                    :loading="optionsLoading" @click="changeOptions"></p-button>
          <p-button v-tooltip.bottom="'Копировать вариант'" icon="pi pi-copy" class="p-button-warning me-2"
                    :loading="optionsLoading" @click="copyOption"></p-button>
          <p-button v-tooltip.bottom="'Удалить вариант'" icon="pi pi-trash" class="p-button-danger me-2"
                    :loading="optionsLoading" @click="deleteOptions"></p-button>
        </span>
      </div>

      <div v-if="options.length">
        <p-button v-tooltip.bottom="'Выделить все'" icon="pi pi-check" class="p-button-warning"
                  :loading="elementsLoading" @click="selectedAllOptions"></p-button>
        <p-button v-tooltip.left="'Снять выделение'" icon="pi pi-times" class="p-button-danger ms-2"
                  :loading="elementsLoading" @click="clearAllOptions"></p-button>
        <p-button v-if="optionsSelected.length"
                  v-tooltip.left="'Показать выбранные'" icon="pi pi-bars" class="p-button-warning ms-2"
                  @click="optionsSelectedShow = !optionsSelectedShow"
        ></p-button>
      </div>
    </div>

    <div v-if="optionsSelectedShow" class="position-absolute bg-white end-0 bottom-0 p-2" style="min-width: 230px">
      <div class="position-relative pt-4">
        <p-button v-tooltip.left="'Закрыть'" icon="pi pi-times" class="position-absolute p-button-raised end-0 top-0"
                  style="width: 25px;height: 25px;"
                  @click="optionsSelectedShow = !optionsSelectedShow"></p-button>
        <div v-for="item of optionsSelected" class="d-flex justify-content-between align-items-center my-1">
          <div>@{{ item.name }}</div>
          <p-button v-tooltip.left="'Снять выделение'" icon="pi pi-times" class="p-button-danger p-button-outlined"
                    style="width: 25px;height: 25px"
                    @click="unselectedOption(item.id)"></p-button>
        </div>
      </div>
    </div>

    <p-dialog v-model:visible="optionsModal.display" :modal="true">
      <template #header>
        <h4>@{{ optionsModal.title }}</h4>
      </template>

      <div v-if="queryParam.dbAction !== 'deleteElements'">
        <div class="row">
          <!-- Основное -->
          <div class="col">
            <!-- Имя -->
            <div v-if="optionsModal.single" class="input-group my-2">
              <span class="input-group-text">Имя варианта</span>
              <p-input-text class="form-control" v-model="option.name" autofocus></p-input-text>
            </div>
            <!-- Единица измерения -->
            <div class="input-group my-2">
              <p-toggle-button v-if="!optionsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                               v-tooltip.bottom="'Не редактировать'"
                               v-model="fieldChange.unitId"
              ></p-toggle-button>
              <span class="input-group-text">Единица измерения</span>
              <p-select class="col" option-label="name" option-value="id"
                        :editable="true"
                        :disabled="!fieldChange.unitId"
                        :options="units"
                        v-model="option.unitId">
              </p-select>
            </div>
            <!-- Входная цена -->
            <div class="col text-center">Входная цена</div>
            <div class="input-group my-2">
              <!-- Валюта -->
              <p-toggle-button v-if="!optionsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                               v-tooltip.bottom="'Не редактировать'"
                               v-model="fieldChange.moneyInputId"
              ></p-toggle-button>
              <span class="input-group-text">Валюта</span>
              <p-select class="col" option-label="shortName" option-value="id"
                        :disabled="!fieldChange.moneyInputId"
                        :options="money" v-model="option.moneyInputId"
              ></p-select>
              <!-- Сумма -->
              <template v-if="optionsModal.single">
                <span class="input-group-text">Сумма</span>
                <p-input-number mode="decimal"
                                :min-fraction-digits="2"
                                v-model="option.inputPrice"
                ></p-input-number>
              </template>
            </div>
            <!-- Выходная цена -->
            <div class="col-12 text-center">Розничная цена</div>
            <div class="input-group my-2">
              <!-- Валюта -->
              <span class="input-group-text">Валюта</span>
              <p-select option-label="shortName" option-value="id"
                        :disabled="!fieldChange.moneyOutputId"
                        :options="money"
                        v-model="option.moneyOutputId">
              </p-select>
              <!-- Наценка -->
              <span class="input-group-text">Наценка</span>
              <p-input-number v-model="option.percent" class="p-inputtext-sm"
                              show-buttons button-layout="horizontal"
                              :min="0" :max="10000" :step="0.25" suffix=" %"
                              :min-fraction-digits="1"
              ></p-input-number>
              <!-- Сумма -->
              <template v-if="optionsModal.single">
                <span class="input-group-text">Сумма</span>
                <p-input-number v-model="option.outputPrice"
                                :min-fraction-digits="2"
                ></p-input-number>
              </template>
            </div>
            <!-- Доступен -->
            <div class="input-group my-2">
              <p-toggle-button v-if="!optionsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                               v-tooltip.bottom="'Не редактировать'"
                               v-model="fieldChange.activity"
              ></p-toggle-button>
              <span class="input-group-text flex-grow-1">Доступ</span>
              <p-toggle-button on-icon="pi pi-check" off-icon="pi pi-times" class="col"
                               on-label="Активен" off-label="Неактивен"
                               :disabled="!fieldChange.activity"
                               v-model="option.activity"
              ></p-toggle-button>
            </div>
            <!-- Сортировка -->
            <div class="input-group my-2">
              <p-toggle-button v-if="!optionsModal.single" on-icon="pi pi-check" off-icon="pi pi-times"
                               v-tooltip.bottom="'Не редактировать'"
                               v-model="fieldChange.sort"
              ></p-toggle-button>
              <span class="input-group-text">Сортировка</span>
              <p-input-number class="col"
                              :disabled="!fieldChange.sort"
                              v-model="option.sort" :min="0"
              ></p-input-number>
            </div>
            <!-- Показать параметры -->
            <div v-if="!optionsModal.single" class="input-group my-2">
              <span class="input-group-text">Доступ</span>
              <p-toggle-button on-icon="pi pi-check" off-icon="pi pi-times"
                               v-model="fieldChange.property"
              ></p-toggle-button>
            </div>
            <!-- Файлы -->
            <div v-if="optionsModal.single" class="col">
              <input type="file" class="d-none" id="uploadFile" multiple @change="addFile">
              <label class="btn btn-warning me-2" for="uploadFile">Загрузить</label>
              <input type="button" class="btn btn-warning" name="chooseFile" value="Выбрать">
            </div>
            <div class="col">
              <div v-for="(file, id) of files"
                   class="row m-1 d-flex justify-content-center"
                   :class="{'error': file.fileError}"
                   :key="id"
                   :data-id="id"
              >
                <div class="col-3">
                  <img :src="file.src" :alt="file.name" style="max-width: 70px; max-height: 70px">
                </div>
                <span class="col-8 bold">@{{ file.name }}</span>
                <div class="col-1 d-flex align-content-end">
                  <p-button  icon="pi pi-times" @click="removeFile"
                  ></p-button>
                </div>

              </div>
            </div>

          </div>

          <!-- Пользовательские параметры -->
          <div v-if="optionsModal.single || fieldChange.property" class="col overflow-auto" style="max-height: 90vh">
            <div class="form-label text-center mb-3">Параметры</div>
            <template v-for="(prop, key, index) of properties">
              <div v-if="prop.type" class="input-group mb-3" :key="key">
                <span class="input-group-text w-50">@{{ prop.name }}</span>
                <input class="form-control" :type="prop.type" v-model="option.property[key]">
              </div>
              <div v-else class="input-group mb-3" :key="key">
                <span class="input-group-text w-50">@{{ prop.name }}</span>
                <select class="form-select w-50" v-model="option.property[key]">
                  <option value=''>-</option>
                  <option v-for="value of prop.values" :value="value.id">@{{ value.name }}</option>
                </select>
              </div>
            </template>
          </div>
        </div>
      </div>
      <div v-else>
        Удалить элемент(ы)
      </div>

      <template #footer>
        <p-button label="Yes" icon="pi pi-check" :disabled="optionsModal.confirmDisabled" @click="optionsConfirm()"></p-button>
        <p-button label="No" icon="pi pi-times" class="p-button-text" @click="optionsCancel()"></p-button>
      </template>
    </p-dialog>
  </div>
</div>
