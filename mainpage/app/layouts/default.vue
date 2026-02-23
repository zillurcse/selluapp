<template>
  <div class="site-wrapper">
    <TheNavbar />
    <CartDrawer />
    <main class="main-content">
      <slot />
    </main>
    <TheFooter />
  </div>
</template>

<style scoped>
.site-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  padding-top: 80px; /* Offset for fixed navbar */
}
</style>
