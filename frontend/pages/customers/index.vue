<template>
  <h1 class="title-page">לקוחות</h1>
  <q-btn color="primary" label="הוספת לקוח" @click="startAddCustomer" />

  <q-table
    :grid="$q.screen.lt.sm"
    :rows="customersFiltered"
    :columns="columns"
    row-key="name"
    @row-click="onRowClick"
  >
    <template v-slot:top-right>
      <q-input
        borderless
        dense
        debounce="300"
        v-model="filter"
        placeholder="Search"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>
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
  <q-dialog v-model="createCustomer">
    <q-card>
      <q-card-section>
        <div class="text-h6" v-text="'הוספת לקוח'" />
      </q-card-section>

      <q-card-section class="q-pt-none">
        <q-form class="add-product-container">
          <q-input v-model="createCustomerName" label="שם לקוח" />
          <q-input v-model="createCustomerPhone" label="טלפון לקוח" />
          <q-input v-model="createCustomerLocation" label="מיקום לקוח" />
        </q-form>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="ביטול" color="primary" v-close-popup />
        <q-btn flat label="אישור" color="primary" @click="saveCustomer" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
const http = useHttpStore();
const router = useRouter();
import Swal from "sweetalert2";
const columns = [
  { name: "name", label: "שם", sortable: true, field: "name" },
  { name: "phone", label: "טלפון", field: "phone" },
  { name: "location", label: "מיקום", field: "location" },
];

const customers: Ref<any[]> = ref([]);
const filter = ref("");
const customersFiltered = computed(() => {
  return customers.value.filter((customer: any) => {
    return customer.name.toLowerCase().includes(filter.value.toLowerCase());
  });
});

async function init() {
  const res = await http.get("customers");
  customers.value = res;
}

onMounted(() => {
  init();
});

const createCustomer = ref(false);
const createCustomerName = ref("");
const createCustomerPhone = ref("");
const createCustomerLocation = ref("");

function startAddCustomer() {
  createCustomerName.value = "";
  createCustomerPhone.value = "";
  createCustomerLocation.value = "";
  createCustomer.value = true;
}

async function saveCustomer() {
  const res = await http.post("customers", {
    name: createCustomerName.value,
    phone: createCustomerPhone.value,
    location: createCustomerLocation.value,
  });
  if (res) {
    createCustomer.value = false;
    createCustomerName.value = "";
    createCustomerPhone.value = "";
    createCustomerLocation.value = "";
    await init();
    Swal.fire({
      title: "הלקוח עודכן בהצלחה",
      icon: "success",
    });
  } else {
    Swal.fire({
      title: "שגיאה בעדכון הלקוח",
      icon: "error",
    });
  }
}

function onRowClick(e1: any, e2: any) {
  router.push(`/customers/${e2.id}`);
}
</script>
