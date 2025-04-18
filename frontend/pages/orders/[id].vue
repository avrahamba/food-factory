<template>
  <h1 class="title-page">הזמנה {{ route.params.id }}</h1>
  <div class="panel" v-if="order">
    <h2 class="order-title">פרטי הזמנה</h2>
    <div class="detailes-grid">
      <p>שם: {{ order.name }}</p>
      <p>
        שם לקוח: {{ customer.name }}
        <q-btn
          color="primary"
          label="כרטיס לקוח"
          :to="`/customers/${customer.id}`"
        />
      </p>
      <p>טלפון לקוח: {{ customer.phone }}</p>
      <p>תאריך ביצוע: {{ order.date }}</p>
      <p>הצעת מחיר: {{ order.price }} ש"ח</p>
      <p>מחיר מחושב: {{ calcPrice }} ש"ח</p>
    </div>
  </div>
  <q-btn color="primary" label="הוספת מוצר" @click="editItem = true" />
  <q-tabs
    v-model="tab"
    dense
    class="text-grey"
    active-color="primary"
    indicator-color="primary"
    align="justify"
  >
    <q-tab name="customer" label="תצוגה להזמנה" />
    <q-tab name="factory" label="תצוגה למטבח" />
  </q-tabs>
  <q-separator />
  <q-tab-panels v-model="tab" animated>
    <q-tab-panel name="customer">
      <q-table
        :grid="$q.screen.lt.sm"
        :rows="orderProducts"
        :columns="customerColumns"
        row-key="name"
        @row-click="onRowClick"
      >
        <template v-slot:item="props">
          <div
            class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
            :style="props.selected ? 'transform: scale(0.95);' : ''"
          >
            <q-card bordered flat class="bg-grey-1">
              <q-separator />
              <q-list dense>
                <q-item v-for="col in props.cols" :key="col.name">
                  <q-item-section>
                    <q-item-label v-text="col.label" />
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label caption v-text="props.row[col.name]" />
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label> </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label caption>
                      <q-btn
                        color="primary"
                        label="עריכה"
                        @click="onRowClick(null, props.row)"
                      />
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card>
          </div>
        </template>
      </q-table>
    </q-tab-panel>
    <q-tab-panel name="factory">
      <q-table
        :grid="$q.screen.lt.sm"
        :rows="orderProductsForFactory"
        :columns="factoryColumns"
        row-key="name"
        @row-click="onRowClick"
      >
        <template v-slot:body-cell="props">
          <q-td :props="props">
            <q-checkbox
              v-if="['cooked', 'inCar'].includes(props.col.name)"
              v-model="props.row[props.col.name]"
              @update:model-value="
                updateProduct(
                  props.row.product_id,
                  props.col.name,
                  props.row[props.col.name]
                )
              "
            />
            <span
              v-else
              class="product-name"
              v-text="props.row[props.col.name]"
            />
          </q-td>
        </template>

        <template v-slot:item="props">
          <div
            class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
            :style="props.selected ? 'transform: scale(0.95);' : ''"
          >
            <q-card bordered flat class="bg-grey-1">
              <q-separator />
              <q-list dense>
                <q-item v-for="col in props.cols" :key="col.name">
                  <q-item-section>
                    <q-item-label>{{ col.label }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label caption>
                      <q-checkbox
                        v-if="['cooked', 'inCar'].includes(col.name)"
                        v-model="props.row[col.name]"
                        @update:model-value="
                          updateProduct(
                            props.row.product_id,
                            col.name,
                            props.row[col.name]
                          )
                        "
                      />
                      <span
                        v-else
                        class="product-name"
                        v-text="props.row[col.name]"
                      />
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card>
          </div>
        </template>
      </q-table>
    </q-tab-panel>
  </q-tab-panels>
  <q-dialog v-model="editItem">
    <q-card>
      <q-card-section>
        <div class="text-h6" v-text="editItemTitle" />
      </q-card-section>

      <q-card-section class="q-pt-none">
        <q-input v-model="editItemNote" label="הערה" />
        <q-form class="add-product-container">
          <q-select
            v-model="editItemProduct"
            :options="productsForEdit"
            label="מוצר"
          />
          <q-input v-model="editItemCount" label="כמות ללקוח" />
        </q-form>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="ביטול" color="primary" v-close-popup />
        <q-btn flat label="אישור" color="primary" @click="saveItem" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import Swal from "sweetalert2";
const http = useHttpStore();
const route = useRoute();

const customerColumns = [
  {
    name: "name",
    field: "name",
    label: "המוצר",
  },
  {
    name: "note",
    field: "note",
    label: "הערה",
  },
  {
    name: "customerCount",
    field: "customerCount",
    label: "כמות ללקוח",
  },
  {
    name: "count",
    field: "count",
    label: "כמות למטבח",
  },
];

const factoryColumns = [
  {
    name: "name",
    field: "name",
    label: "המוצר",
  },
  {
    name: "customerCount",
    field: "customerCount",
    label: "כמות ללקוח",
  },
  {
    name: "count",
    field: "count",
    label: "כמות למטבח",
  },
  {
    name: "cooked",
    field: "cooked",
    label: "מבושל",
  },
  {
    name: "inCar",
    field: "inCar",
    label: "באוטו",
  },
];

const order = ref<any>(null);
const orderItems = ref<any[]>([]);
const products = ref<any[]>([]);
const calcPrice = ref(0);
const customer = ref<any>(null);

async function init() {
  const resProduct = await http.get(`products`);
  products.value = resProduct;

  const id = route.params.id;
  const res = await http.get(`orders/${id}`);
  order.value = res.order;
  orderItems.value = res.orderItems;
  calcPrice.value = res.calcPrice;
  customer.value = res.customer;
}

onMounted(async () => {
  init();
});

const tab = ref("customer");

const orderProducts = computed(() => {
  return orderItems.value.map((item: any) => {
    const product = products.value.find((p: any) => p.id === item.product_id);
    return {
      id: item.id,
      name: product.name,
      customerCount:
        Math.round(item.count * 100) / 100 + " " + product.unit_type_customer,
      count:
        Math.round((item.count / product.ratio) * 100) / 100 +
        " " +
        product.unit_type,
      note: item.note,
    };
  });
});

const orderProductsForFactory = computed(() => {
  return orderItems.value.reduce((acc: any[], item: any) => {
    const product = products.value.find((p: any) => p.id === item.product_id);
    const exists = acc.find((i: any) => i.product_id === item.product_id);
    if (exists) {
      exists.customerCount += item.count;
      exists.count =
        Math.round((exists.customerCount / product.ratio) * 100) / 100;
    } else {
      acc.push({
        product_id: item.product_id,
        customerCount: item.count,
        count: Math.round((item.count / product.ratio) * 100) / 100,
        name: product.name,
        cooked: !!item.cooked,
        inCar: !!item.in_car,
      });
    }
    return acc;
  }, []);
});

const editItem = ref(false);
const editItemId = ref(null);
const editItemCount = ref(null);
const editItemNote = ref("");
const productsForEdit = computed(() => {
  return products.value.map((p: any) => ({ label: p.name, value: p.id }));
});
const editItemProduct: Ref<{ label: string; value: number } | null> = ref(null);
const editItemTitle = computed(() => {
  return editItemId.value ? "עריכת מוצר" : "הוספת מוצר";
});

function onRowClick(e: any, e2: any) {
  editItemId.value = e2.id;
  const orderItem = orderItems.value.find((i: any) => i.id === e2.id);
  editItemCount.value = orderItem.count;
  editItemNote.value = orderItem.note;
  editItemProduct.value = { label: e2.name, value: orderItem.product_id };
  editItem.value = true;
}

async function saveItem() {
  if (!editItemProduct.value || !editItemCount.value) {
    Swal.fire({
      title: "שגיאה",
      text: "יש למלא את כל השדות",
      icon: "error",
    });
    return;
  }
  if (editItemId.value) {
    const res = await http.put(
      `orders/${route.params.id}/items/${editItemId.value}`,
      {
        count: editItemCount.value,
        product_id: editItemProduct.value.value,
        note: editItemNote.value,
      }
    );
    if (res) {
      editItem.value = false;
      editItemId.value = null;
      editItemCount.value = null;
      editItemNote.value = "";
      editItemProduct.value = null;
      await init();
      Swal.fire({
        title: "המוצר עודכן בהצלחה",
        icon: "success",
      });
    } else {
      Swal.fire({
        title: "המוצר לא עודכן",
        icon: "error",
      });
    }
  } else {
    const res = await http.post(`orders/${route.params.id}/items`, {
      count: editItemCount.value,
      product_id: editItemProduct.value.value,
      note: editItemNote.value,
    });
    if (res) {
      editItem.value = false;
      editItemId.value = null;
      editItemCount.value = null;
      editItemNote.value = "";
      editItemProduct.value = null;
      await init();
      Swal.fire({
        title: "המוצר עודכן בהצלחה",
        icon: "success",
      });
    } else {
      Swal.fire({
        title: "המוצר לא עודכן",
        icon: "error",
      });
    }
  }
}

async function updateProduct(product_id: number, field: string, value: any) {
  const res = await http.put(`orders/${route.params.id}/items`, {
    product_id,
    field,
    value,
  });
  if (res) {
    await init();
  }
}
</script>

<style scoped>
.detailes-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}
.order-title {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 10px;
  color: #1166b6;
}
</style>
