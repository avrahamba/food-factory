<template>
  <div>
    <h1 class="title-page">הזמנות</h1>
    <q-btn color="primary" label="הוספת הזמנה" to="/orders/new" />

    <q-table
      :grid="$q.screen.lt.sm"
      title="הזמנות"
      :rows="orders"
      :columns="columns"
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
  </div>
</template>

<script setup lang="ts">
const http = useHttpStore();
const router = useRouter();
const columns = [
  {
    name: "id",
    field: "id",
    label: "Order ID",
  },
  {
    name: "date",
    field: "date",
    label: "תאריך ביצוע",
  },
  {
    name: "name",
    field: "name",
    label: "שם",
  },
  {
    name: "customerName",
    field: "customer_name",
    label: "שם לקוח",
  },

  {
    name: "customerPhone",
    field: "customer_phone",
    label: "טלפון לקוח",
  },
  {
    name: "price",
    field: "price",
    label: "מחיר",
  },
];

const orders = ref([]);

onMounted(async () => {
  const res = await http.get("orders");
  orders.value = res;
});

function onRowClick(e: any, e2: any) {
  router.push(`/orders/${e2.id}`);
}
</script>
<style scoped>
.catalog-container {
  padding: 20px;
}
.add-product-container {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}
</style>
