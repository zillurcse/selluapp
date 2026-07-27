export type ProductFormErrors = Record<string, string[]>

export function useProductValidation() {
  const getVariantLabel = (variant: any, index: number) =>
    variant?.attributes?.map((attribute: any) => attribute.value).join(' / ') || `Variant ${index + 1}`

  const validateVariantRows = (variants: any[], clientErrors: ProductFormErrors) => {
    const seenSkus = new Set<string>()

    variants.forEach((variant, index) => {
      const label = getVariantLabel(variant, index)
      const isActive = variant.is_active !== false

      if (isActive) {
        if (variant.price === '' || variant.price === null || Number(variant.price) < 0) {
          clientErrors[`variants.${index}.price`] = [`${label}: enter a valid price.`]
        }

        if (variant.stock_qty === '' || variant.stock_qty === null || Number(variant.stock_qty) < 0) {
          clientErrors[`variants.${index}.stock_qty`] = [`${label}: enter a valid stock quantity.`]
        }
      }

      if (!variant.attributes?.length) {
        clientErrors[`variants.${index}.attributes`] = [`${label}: select at least one attribute value.`]
      }

      if (variant.sku) {
        const sku = String(variant.sku).trim().toUpperCase()

        if (seenSkus.has(sku)) {
          clientErrors[`variants.${index}.sku`] = [`${label}: duplicate SKU in variant list.`]
        } else {
          seenSkus.add(sku)
        }
      }
    })
  }

  const buildProductValidationErrors = (form: any, generatedVariants: any[]) => {
    const clientErrors: ProductFormErrors = {}

    if (!form.name?.trim()) clientErrors.name = ['Product name is required.']
    if (!form.category_ids?.length) clientErrors.category_ids = ['Select at least one category.']
    if (!form.sku?.trim()) clientErrors.sku = ['SKU is required.']
    if (!form.product_code?.trim()) clientErrors.product_code = ['Product code is required.']
    if (form.sale_price === '' || form.sale_price === null) clientErrors.sale_price = ['Sale price is required.']
    if (form.weight === '' || form.weight === null || Number(form.weight) < 0) clientErrors.weight = ['Weight is required.']
    if (!form.description?.replace(/<[^>]*>/g, '').trim()) clientErrors.description = ['Product description is required.']

    if (form.has_variants) {
      if (!generatedVariants.length) {
        clientErrors.variants = ['Add at least one variant when variants are enabled.']
      } else {
        validateVariantRows(generatedVariants, clientErrors)
      }
    } else if (form.stock_qty === '' || form.stock_qty === null) {
      clientErrors.stock_qty = ['Stock quantity is required.']
    }

    if (form.is_dropshipping && !form.dropshipping_url?.trim()) {
      clientErrors.dropshipping_url = ['Dropshipping product URL is required.']
    }

    if (form.video_url?.trim() && !/^https?:\/\/.+/i.test(form.video_url.trim())) {
      clientErrors.video_url = ['Enter a valid video URL.']
    }

    if (form.discount_value !== '' && form.discount_value !== null && !form.discount_type) {
      clientErrors.discount_type = ['Select a discount type.']
    }

    if (form.discount_type && (form.discount_value === '' || form.discount_value === null)) {
      clientErrors.discount_value = ['Enter a discount value.']
    }

    const salePrice = Number(form.sale_price)
    const discountPrice = Number(form.discount_price)

    if (!Number.isNaN(salePrice) && !Number.isNaN(discountPrice) && form.discount_price !== '' && discountPrice >= salePrice) {
      clientErrors.discount_price = ['Discount price must be less than the sale price.']
    }

    return clientErrors
  }

  return {
    buildProductValidationErrors,
    validateVariantRows,
    getVariantLabel,
  }
}
