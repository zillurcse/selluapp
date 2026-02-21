<template>
  <div class="site-wrapper">
    <TheNavbar />
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
}
</style>
