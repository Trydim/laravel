<!--$admin = $main->getSettings('admin');
$catalogProperties = in_array('catalog', $main->getSideMenu());-->

<div class="row container m-auto" id="settingForm">
  @if($isAdmin)
  <div class="col-6 border" id="mailForm">
    <h3 class="col text-center">Настройка почты</h3>
    <div class="form-floating my-3">
      <p-input-text v-model="mail.managerTarget" :class="'form-control'" placeholder="Почта"
      ></p-input-text>
      <label>Почта для получения заказов</label>
    </div>
    <div class="form-floating mb-3">
      <p-input-text v-model="mail.managerTargetCopy" :class="'form-control'" placeholder="Почта"
      ></p-input-text>
      <label>Копия письма</label>
    </div>
    <div class="form-floating mb-3">
      <p-input-text v-model="mail.subject" :class="'form-control'" placeholder="Тема"
      ></p-input-text>
      <label>Тема письма</label>
    </div>
    <div class="form-floating mb-3">
      <p-input-text v-model="mail.fromName" :class="'form-control'" placeholder="Имя"
      ></p-input-text>
      <label>Имя отправителя</label>
    </div>
  </div>
  @endif

  <div class="col-6 border">
    <h3 class="col text-center">Пользователь</h3>
    <div class="form-floating my-3">
      <p-input-text v-model="user.login" :class="'form-control'" placeholder="Почта"
      ></p-input-text>
      <label>Логин</label>
    </div>
    <div class="form-floating mb-3">
      <p-input-text type="password" v-model="user.password" :class="'form-control'" placeholder="Пароль"
      ></p-input-text>
      <label>Новый Пароль</label>
    </div>
    <div class="form-floating mb-3">
      <p-input-text type="password" v-model="user.passwordRepeat" :class="'form-control'" placeholder="Пароль"
      ></p-input-text>
      <label>Повторите Пароль</label>
    </div>
    <div class="form-floating mb-3 d-none">
      <p-checkbox v-model="user.onlyOne" :class="'form-control'" placeholder="Вход"
      ></p-checkbox>
      <label>Запретить одновременный вход</label>
    </div>
  </div>

  @if($isAdmin)
    <div class="col-6 border">
      <h3 class="col text-center">Управление доступом</h3>

      <div class="input-group my-3">
        <span class="input-group-text">Добавить тип доступа</span>
        <p-input-text v-model="permission.name" :class="'form-control'"
        ></p-input-text>
        <p-button v-tooltip.bottom="'Добавить тип доступа'" icon="pi pi-plus-circle" class="p-button-success"
                  @click="addPermission"></p-button>
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text">Тип доступа</span>
        <p-select option-label="name" option-value="id"
                  :options="permissionsData"
                  v-model="permission.id"
                  :class="'col'"
        ></p-select>
        <p-button v-tooltip.bottom="'Удалить тип доступа'" icon="pi pi-trash" class="p-button-danger"
                  @click="removePermission"></p-button>
      </div>

      <div class="col mb-3">
        <p class="col-12 mt-2 text-center">Доступные меню</p>
        <p-picklist v-model="permission.menu" data-key="id"
                    list-style="height:220px"
                    :class="'w-100'"
                    @selection-change="pickedChange"
        >
          <template #source>
            Возможные
          </template>
          <template #target>
            Доступные
          </template>
          <template #item="slotProps">
            <div class="product-item">
              @{{ slotProps.item.name }}
            </div>
          </template>
        </p-picklist>
      </div>
    </div>

    <div class="col-6 border">
      <h3 class="col-12 text-center">Настройки менеджеров</h3>

      <div class="input-group my-3">
        <span class="input-group-text flex-grow-1">Дополнительные поля менеджеров</span>
        <p-button v-tooltip.bottom="'Добавить новое поле'" icon="pi pi-plus-circle" class="p-button-success"
                  @click="addCustomField"></p-button>
      </div>

      <template v-for="(item, key) of managerFields" :key="key">
        <div class="input-group mb-1">
          <p-input-text v-model="item.name" class="form-control"
          ></p-input-text>
          <p-select option-label="name" option-value="id"
                    :options="managerFieldTypes"
                    v-model="item.type"
                    class="'col'"
          ></p-select>
          <p-button v-tooltip.bottom="'Удалить поле'" icon="pi pi-times" class="p-button-danger"
                    @click="removeCustomField(key)"></p-button>
        </div>
      </template>
    </div>

    <div class="col-6 border" id="rateForm">
      <h3 class="col-12 text-center">Курсы валют</h3>

      <div class="col-12 row">
        <p class="col-8">Автоматически обновлять курсы</p>
        <div class="col-4 d-inline-flex">
          <p class="col text-center">Нет</p>
          <p-switch v-model="rate.autoRefresh"
          ></p-switch>
          <p class="col text-center">Да</p>
        </div>
      </div>

      <div v-if="!rate.autoRefresh" class="col-12 text-center mb-3">
        <p-button v-tooltip.bottom="'Редактировать курсы'" icon="pi pi-sliders-h" class="p-button-success"
                  label="Редактировать курсы"
                  @click="editRate"
        ></p-button>
      </div>
    </div>
  @endif

  <div class="col-12 text-center">
    <p-button v-tooltip.bottom="'Сохранить'" icon="pi pi-save" class="p-button-primary m-3"
              label="Сохранить"
              @click="saveSetting"
    ></p-button>
  </div>

  @if($properties)
    <hr>
    <div class="col-12" id="propertiesWrap($event)">
      <p-accordion @tab-open="openAccordion($event)">
        <p-accordion-tab header="Редактировать параметры каталога">
          <p-table v-if="propertiesData"
                   :value="propertiesData"
                   :loading="propertiesLoading"
                   :resizable-columns="true" column-resize-mode="fit" show-gridlines
                   selection-mode="single" :meta-key-selection="false"
                   :scrollable="true"
                   responsive-layout="scroll"
                   v-model:selection="propertiesSelected"
                   @dblclick="changeProperty($event)"
                   :bodyClass="'text-center'"
          >
            <p-t-column field="name" header="{{ __('db.property.name') }}"></p-t-column>
            <p-t-column field="code" :sortable="true" header="{{ __('db.property.code') }}"></p-t-column>
            <p-t-column field="type" header="{{ __('db.property.type') }}"></p-t-column>
          </p-table>

          <div class="my-3 text-center">
            <p-button v-tooltip.bottom="'Добавить'" icon="pi pi-plus-circle" class="p-button-warning mx-1"
                      :loading="propertiesLoading" @click="createProperty"></p-button>
            <p-button v-tooltip.bottom="'Изменить'" icon="pi pi-cog" class="p-button-warning mx-1"
                      :loading="propertiesLoading" @click="changeProperty"></p-button>
            <p-button v-tooltip.bottom="'Удалить'" icon="pi pi-trash" class="p-button-danger mx-1"
                      :loading="propertiesLoading" @click="deleteProperty"></p-button>
          </div>
        </p-accordion-tab>
      </p-accordion>

      <p-dialog v-model:visible="propertiesModal.display" :modal="true">
        <template #header>
          <h4>@{{ propertiesModal.title }}</h4>
        </template>

        <div v-if="queryParam.dbAction !== 'deleteProperty'" style="width: 600px">
          <!-- Имя -->
          <div class="col-12 row my-1">
            <div class="col">Название свойства:</div>
            <div class="col">
              <p-input-text class="w-100" v-model="property.name" autofocus></p-input-text>
            </div>
          </div>
          <!-- Код свойства -->
          <div class="col-12 row my-1">
            <div class="col">Код свойства:</div>
            <div class="col">
              <p-input-text class="w-100" v-model="property.code"></p-input-text>
            </div>
          </div>

          <!-- Тип данных -->
          <div class="col-12 row my-1">
            <div class="col">Тип данных:</div>
            <div class="col">
              <p-select class="w-100" option-label="name"
                        :options="propertiesTypes"
                        option-group-label="label" option-group-children="items"
                        option-value="id" option-label="name"
                        v-model="property.type">
              </p-select>
            </div>
          </div>

          <!-- Составной тип-->
          <template v-if="property.type === 'select'">
            <div class="col-12 row mb-1">
              <div class="col">Дополнительные поля свойства (имя есть):</div>
              <div class="col">
                <p-button v-tooltip.bottom="'Добавить поле'" icon="pi pi-plus-circle" class="w-100 p-button-raised"
                          label="Добавить поле"
                          @click="addPropertyField"></p-button>
              </div>
            </div>

            <div v-for="(field, key) of property.fields" class="row mb-1 border" :key="key">
              <div class="col-5 text-center">
                <p-input-text class="w-100"
                              v-model="field.name"
                ></p-input-text>
              </div>
              <div class="col-6">
                <p-select class="w-100" option-label="name"
                          :options="propertiesDataBaseTypes"
                          option-value="id" option-label="name"
                          v-model="field.type">
                </p-select>
              </div>
              <div class="col-1 text-center">
                <p-button v-tooltip.bottom="'Удалить поле'" icon="pi pi-times" class="p-button-danger"
                          @click="removePropertyField(key)"></p-button>
              </div>
            </div>
          </template>
        </div>
        <div v-else>
          Удалить свойство
        </div>

        <template #footer>
          <p-button label="Yes" icon="pi pi-check" :disabled="propertiesModal.confirmDisabled" @click="propertiesConfirm"></p-button>
          <p-button label="No" icon="pi pi-times" class="p-button-text" @click="propertiesCancel"></p-button>
        </template>
      </p-dialog>
    </div>
  @endif
</div>

