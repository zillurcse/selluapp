<template>
  <div class="min-h-screen pt-24 pb-12 bg-white">
    <header class="container mx-auto px-4 mb-20 animate-fade-in">
      <div class="max-w-3xl mx-auto text-center space-y-4">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
          Shipping Policy
        </h1>
        <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed">
          Everything you need to know about our delivery process and timelines.
        </p>
      </div>
    </header>

    <main class="container mx-auto px-4 max-w-3xl animate-slide-up">
      <div class="prose prose-gray max-w-none space-y-12">
        <!-- Processing Time -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Order Processing</h2>
          <p class="text-gray-600 leading-relaxed">
            All orders are processed within 1-2 business days. Orders are not shipped or delivered on weekends or holidays.
          </p>
          <p class="text-gray-600 leading-relaxed">
            If we are experiencing a high volume of orders, shipments may be delayed by a few days. Please allow additional days in transit for delivery. If there will be a significant delay in shipment of your order, we will contact you via email or telephone.
          </p>
        </section>

        <!-- Shipping Rates -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Shipping Rates & Estimates</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="border-b border-gray-100 text-gray-400">
                  <th class="py-4 font-bold uppercase tracking-widest text-[0.65rem]">Method</th>
                  <th class="py-4 font-bold uppercase tracking-widest text-[0.65rem]">Estimated Time</th>
                  <th class="py-4 font-bold uppercase tracking-widest text-[0.65rem] text-right">Cost</th>
                </tr>
              </thead>
              <tbody class="text-gray-600 divide-y divide-gray-50">
                <tr>
                  <td class="py-4 font-medium text-gray-900">Standard Shipping</td>
                  <td class="py-4">3-5 business days</td>
                  <td class="py-4 text-right">Free</td>
                </tr>
                <tr>
                  <td class="py-4 font-medium text-gray-900">Express Shipping</td>
                  <td class="py-4">1-2 business days</td>
                  <td class="py-4 text-right">$15.00</td>
                </tr>
                <tr>
                  <td class="py-4 font-medium text-gray-900">International</td>
                  <td class="py-4">7-14 business days</td>
                  <td class="py-4 text-right">$25.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Tracking -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Shipment Confirmation & Tracking</h2>
          <p class="text-gray-600 leading-relaxed">
            You will receive a Shipment Confirmation email once your order has shipped containing your tracking number(s). The tracking number will be active within 24 hours.
          </p>
        </section>

        <!-- Customs & Duties -->
        <section class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Customs, Duties & Taxes</h2>
          <p class="text-gray-600 leading-relaxed">
            Modern is not responsible for any customs and taxes applied to your order. All fees applied during or after shipping are the responsibility of the customer (tariffs, taxes, etc.).
          </p>
        </section>

        <!-- Damages -->
        <section class="space-y-4 pb-20">
          <h2 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-4">Damages</h2>
          <p class="text-gray-600 leading-relaxed">
            If you received your order damaged, please contact the shipment carrier to file a claim. Please save all packaging materials and damaged goods before filing a claim.
          </p>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
useHead({
  title: 'Shipping Policy',
  meta: [
    { name: 'description', content: 'Our delivery process and timelines.' }
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
