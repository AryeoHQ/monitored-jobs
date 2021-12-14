<template>
  <div class="space-y-2">
    <h3>Tags</h3>
    <Tag v-for="tag in tags" :key="tag" :tag="tag" />
  </div>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3'
import Tag from '@/components/Filters/Tag.vue'

export default {
  components: {
    Tag
  },

  setup() {
    const page = usePage()

    return {
      tags: page.props.value.tags
    }
  }
}
</script>