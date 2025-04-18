<template>
  <h1 v-if="customer" class="title-page">כרטיס לקוח {{ customer.name }}</h1>
  <h1 v-else class="title-page">לקוח לא נמצא</h1>
  <q-card v-if="customer" class="q-pa-md">
    <p>שם: {{ customer.name }}</p>
    <p>טלפון: {{ customer.phone }}</p>
    <p>מיקום: {{ customer.location }}</p>
  </q-card>
  <h2 v-if="customer && orders.length" class="title-page">הזמנות לקוח</h2>
  <q-table
    v-if="customer && orders.length"
    :grid="$q.screen.lt.sm"
    :rows="orders"
    :columns="ordersColumns"
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
            <q-item
              v-for="col in props.cols"
              :key="col.name"
              clickable
              @click="onRowClick(null, props.row)"
            >
              <q-item-section>
                <q-item-label v-text="col.label" />
              </q-item-section>
              <q-item-section side>
                <q-item-label caption v-text="props.row[col.name]" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-card>
      </div>
    </template>
  </q-table>
</template>

<script setup lang="ts">
const http = useHttpStore();
const route = useRoute();
const router = useRouter();
const id = route.params.id;

const customer: Ref<any> = ref(null);
const orders: Ref<any[]> = ref([]);
async function init() {
  const res = await http.get(`customers/${id}`);
  customer.value = res.customer;
  orders.value = res.orders;
}

onMounted(async () => {
  init();
});

const ordersColumns = [
  {
    name: "id",
    required: true,
    label: "ID",
    field: "id",
    sortable: true,
  },
  {
    name: "name",
    label: "שם הזמנה",
    field: "name",
    sortable: true,
  },
  {
    name: "price",
    label: "מחיר",
    field: "price",
    sortable: true,
  },
  {
    name: "date",
    label: "תאריך ביצוע",
    field: "date",
    sortable: true,
  },
];

function onRowClick(e: any, e2: any) {
  router.push(`/orders/${e2.id}`);
}
</script>
