<template>
  <q-layout class="main-layout">
    <q-btn
      flat
      dense
      round
      icon="menu"
      color="primary"
      aria-label="Menu"
      class="menu-btn"
      @click="drawer = !drawer"
    />

    <!-- Main Content -->
    <div class="main">
      <slot />
    </div>

    <!-- Mobile Sidebar -->
    <q-drawer
      v-model="drawer"
      side="right"
      overlay
      behavior="mobile"
      :width="200"
    >
      <q-scroll-area class="fit" :horizontal-thumb-style="{ opacity: '0' }">
        <q-list padding>
          <q-item clickable v-ripple to="/catalog">
            <q-item-section avatar>
              <q-icon name="inbox" />
            </q-item-section>
            <q-item-section v-text="'קטלוג'" />
          </q-item>

          <q-item clickable v-ripple to="/orders">
            <q-item-section avatar>
              <q-icon name="star" />
            </q-item-section>
            <q-item-section v-text="'הזמנות'" />
          </q-item>
          <q-item clickable v-ripple to="/customers">
            <q-item-section avatar>
              <q-icon name="person" />
            </q-item-section>
            <q-item-section v-text="'לקוחות'" />
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>
  </q-layout>
</template>

<script setup lang="ts">
const drawer = ref(false);
</script>

<style scoped>
.main-layout {
  display: flex;
  min-height: 100vh;
}
.sidebar {
  width: 200px;
  background-color: #2d3748;
  color: #fff;
  padding: 40px 20px;
  .nav {
    display: flex;
    flex-direction: column;
  }
}

.main {
  flex: 1;
  padding: 20px;
}
.btn {
  margin-bottom: 10px;
}

.menu-btn {
  position: fixed;
  top: 20px;
  right: 20px;
}
</style>
