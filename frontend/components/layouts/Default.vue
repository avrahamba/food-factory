<template>
  <div class="main-layout">
    <!-- Sidebar -->
    <div class="sidebar">
      <nav class="nav">
        <q-btn
          to="/admin/catalog"
          variant="ghost"
          label="קטלוג"
          color="primary"
          class="btn"
        />
        <q-btn
          to="/admin/orders"
          variant="ghost"
          label="הזמנות"
          color="primary"
          class="btn"
        />
      </nav>
    </div>

    <!-- Main Content -->
    <div class="main">
      <slot />
    </div>
  </div>
</template>

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
</style>
