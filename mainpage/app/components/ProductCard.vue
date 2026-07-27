<template>
  <div class="product-card group relative" @click="navigateToProduct">
    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-[var(--muted)]">
      <img
        :src="product.image"
        :alt="product.name"
        class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
        loading="lazy"
      />

      <div class="absolute top-3 left-3 right-3 z-10 flex justify-between items-start pointer-events-none">
        <div class="flex flex-col gap-1.5">
          <span
            v-if="discountPercent"
            class="px-2.5 py-1 bg-[var(--sale)] text-white text-[10px] font-semibold uppercase tracking-wide rounded-lg"
          >
            -{{ discountPercent }}%
          </span>
          <span
            v-else-if="product.is_featured"
            class="px-2.5 py-1 bg-[var(--primary)] text-white text-[10px] font-semibold uppercase tracking-wide rounded-lg"
          >
            Featured
          </span>
        </div>
        <button
          @click.stop="handleWishlist"
          aria-label="Toggle wishlist"
          class="p-2 rounded-full bg-white/90 shadow-sm transition-all pointer-events-auto hover:scale-105"
          :class="isInWishlist(product.id) ? 'text-[var(--sale)]' : 'text-gray-700 hover:text-[var(--sale)]'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="{ 'fill-current': isInWishlist(product.id) }"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </button>
      </div>

      <div class="absolute inset-x-0 bottom-0 p-3 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 pointer-events-none max-sm:opacity-100 max-sm:translate-y-0 max-sm:pointer-events-auto">
        <button
          class="w-full py-2.5 bg-white text-gray-900 rounded-xl font-semibold text-xs tracking-wide shadow-md flex items-center justify-center gap-2 pointer-events-auto hover:bg-gray-900 hover:text-white transition-colors"
          @click.stop="$emit('add-to-cart', product)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          Add to cart
        </button>
      </div>
    </div>

    <div class="mt-3 space-y-1 px-0.5">
      <div class="flex items-center justify-between gap-2">
        <span class="text-[11px] font-medium uppercase tracking-wider text-[var(--muted-foreground)] truncate">
          {{ product.category }}
        </span>
        <div v-if="product.rating" class="flex items-center gap-1 shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
          <span class="text-[11px] font-semibold text-gray-800">{{ product.rating }}</span>
        </div>
      </div>

      <h3
        class="text-sm sm:text-[15px] font-semibold text-gray-900 leading-snug line-clamp-2 group-hover:opacity-70 transition-opacity"
        style="font-family: var(--font-heading)"
      >
        {{ product.name }}
      </h3>

      <div class="flex items-baseline gap-2 pt-0.5">
        <span class="text-base font-bold text-gray-900 tabular-nums">৳{{ displayPrice }}</span>
        <span
          v-if="compareAtPrice"
          class="text-xs text-[var(--muted-foreground)] line-through tabular-nums"
        >৳{{ compareAtPrice }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { toast } from 'vue-sonner'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

defineEmits(['add-to-cart'])

const { isInWishlist, toggleWishlist } = useWishlist()

const displayPrice = computed(() => {
  const p = props.product
  if (p.discount_price && Number(p.discount_price) > 0 && Number(p.discount_price) < Number(p.price)) {
    return p.discount_price
  }
  return p.price
})

const compareAtPrice = computed(() => {
  const p = props.product
  if (p.discount_price && Number(p.discount_price) > 0 && Number(p.discount_price) < Number(p.price)) {
    return p.price
  }
  return null
})

const discountPercent = computed(() => {
  if (!compareAtPrice.value) return null
  const pct = Math.round((1 - Number(displayPrice.value) / Number(compareAtPrice.value)) * 100)
  return pct > 0 ? pct : null
})

const navigateToProduct = () => {
  navigateTo(`/product/${props.product.slug}`)
}

const handleWishlist = () => {
  if (!props.product?.id) return
  const added = toggleWishlist(props.product)
  if (added) {
    toast.success(`${props.product.name} added to wishlist`)
  } else {
    toast.info(`${props.product.name} removed from wishlist`)
  }
}
</script>

<style scoped>
.product-card {
  cursor: pointer;
}
</style>
