<template>
  <div class="min-h-screen pt-24 pb-12 bg-white">
    <header class="container mx-auto px-4 mb-20 animate-fade-in">
      <div class="max-w-3xl mx-auto text-center space-y-4">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
          Returns Policy
        </h1>
        <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed">
          Transparent and simple returns for your peace of mind.
        </p>
      </div>
    </header>

    <main class="container mx-auto px-4 max-w-3xl animate-slide-up">
      <div class="prose prose-gray max-w-none space-y-12">
        <!-- Overview -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Return Conditions</h2>
          <p class="text-gray-600 leading-relaxed">
            We want you to be completely satisfied with your purchase. If you're not, you can return your items within **30 days** of delivery for a full refund or exchange.
          </p>
          <ul class="text-gray-600 space-y-2 list-disc pl-5">
            <li>Items must be in original condition: unworn, unwashed, and with all tags attached.</li>
            <li>Original packaging must be included and undamaged.</li>
            <li>Proof of purchase is required for all returns.</li>
          </ul>
        </section>

        <!-- Process -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">How to Return</h2>
          <div class="space-y-6">
            <div class="flex gap-6 items-start">
              <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center shrink-0 text-xs font-bold">1</span>
              <div>
                <h3 class="font-bold text-gray-900">Initiate Request</h3>
                <p class="text-gray-600 text-sm">Contact our support team or visit our <NuxtLink to="/contact" class="text-gray-900 underline">Contact Page</NuxtLink> to start your return.</p>
              </div>
            </div>
            <div class="flex gap-6 items-start">
              <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center shrink-0 text-xs font-bold">2</span>
              <div>
                <h3 class="font-bold text-gray-900">Pack Your Items</h3>
                <p class="text-gray-600 text-sm">Securely pack your items in the original packaging, including the return authorization form.</p>
              </div>
            </div>
            <div class="flex gap-6 items-start">
              <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center shrink-0 text-xs font-bold">3</span>
              <div>
                <h3 class="font-bold text-gray-900">Ship It Back</h3>
                <p class="text-gray-600 text-sm">Drop off your package at any authorized shipping location. We recommend using a trackable service.</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Refunds -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Refunds</h2>
          <p class="text-gray-600 leading-relaxed">
            Once we receive and inspect your return, we will notify you of the approval or rejection of your refund. Approved refunds are processed within 5-10 business days and automatically applied to your original method of payment.
          </p>
        </section>

        <!-- Non-returnable -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Non-Returnable Items</h2>
          <p class="text-gray-600 leading-relaxed">
            Certain items cannot be returned for hygiene or safety reasons, including:
          </p>
          <ul class="text-gray-600 space-y-2 list-disc pl-5">
            <li>Opened personal care products</li>
            <li>Underwear and swimwear</li>
            <li>Final sale items clearly marked as non-returnable</li>
          </ul>
        </section>

        <!-- Help -->
        <section class="space-y-4 pb-20">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Still Need Help?</h2>
          <p class="text-gray-600 leading-relaxed">
            If you have any questions regarding our return policy, please reach out to us through our <NuxtLink to="/contact" class="text-gray-900 underline">Contact Page</NuxtLink>.
          </p>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
useHead({
  title: 'Returns Policy',
  meta: [
    { name: 'description', content: 'Our transparent and simple returns process.' }
  ]
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-slide-up {
  opacity: 0;
  animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
