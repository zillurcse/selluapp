<template>
  <section v-if="lookbook !== null && lookbook.lookbook_status == '1'" id="lookbook-section" class="section-pad bg-[var(--surface)]">
    <div class="container mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
        <div class="relative rounded-2xl overflow-hidden aspect-[4/5] lg:aspect-auto lg:h-[520px] bg-[var(--muted)]">
          <img
            :src="lookbook.lookbook_image || heroImg"
            :alt="lookbook.lookbook_title || 'Lookbook'"
            class="w-full h-full object-cover"
            loading="lazy"
          />
        </div>

        <div class="flex flex-col">
          <span v-if="lookbook.lookbook_subtitle" class="section-label mb-3">{{ lookbook.lookbook_subtitle }}</span>
          <h2
            class="section-title text-3xl md:text-4xl lg:text-5xl mb-5"
            v-html="lookbook.lookbook_title || 'Crafted for everyday'"
          ></h2>
          <p class="text-[var(--muted-foreground)] text-base leading-relaxed mb-8 max-w-md">
            {{ lookbook.lookbook_description || 'Discover pieces designed with care — thoughtful materials, lasting quality, and a clean modern look.' }}
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 pt-8 border-t border-[var(--border)]">
            <div class="space-y-2">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl bg-white border border-[var(--border)]">
                {{ lookbook.feature_1_icon || '✓' }}
              </div>
              <h4 class="font-semibold text-gray-900" style="font-family: var(--font-heading)">
                {{ lookbook.feature_1_title || 'Quality first' }}
              </h4>
              <p class="text-sm text-[var(--muted-foreground)]">
                {{ lookbook.feature_1_desc || 'Carefully selected materials.' }}
              </p>
            </div>
            <div class="space-y-2">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl bg-white border border-[var(--border)]">
                {{ lookbook.feature_2_icon || '★' }}
              </div>
              <h4 class="font-semibold text-gray-900" style="font-family: var(--font-heading)">
                {{ lookbook.feature_2_title || 'Made to last' }}
              </h4>
              <p class="text-sm text-[var(--muted-foreground)]">
                {{ lookbook.feature_2_desc || 'Built for everyday use.' }}
              </p>
            </div>
          </div>

          <NuxtLink :to="lookbook.shop_link || '/shop'" class="btn-primary w-fit">
            {{ lookbook.button_text || 'Shop the collection' }}
          </NuxtLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import heroImg from '~/assets/img/hero.png'

defineProps({
  lookbook: {
    type: Object,
    default: null
  }
})
</script>
