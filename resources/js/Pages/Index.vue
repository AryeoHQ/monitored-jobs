<template>
  <div class="flex gap-4 h-screen">
    <div class="bg-gray-50 w-64 p-4 space-y-4">
      <div class="flex items-center space-x-2 relative">
        <input type="text" v-model="searchTerm" class="border border-gray-200 rounded p-1 w-full" placeholder="Search..." />
        <div v-if="loading" class="absolute left-auto inset-x-2 ml-8 rounded-full border-4 border-dashed border-t-transparent border-blue-400 inline-block w-6 h-6 animate-spin" />
      </div>

      <StatusFilter />
      <TagFilter />
    </div>

    <div class="flex-1 p-4 overflow-x-auto">
      <div>
        Found {{ monitoredJobs.length }} result(s).
      </div>

      <MonitoredJob v-if="monitoredJobs.length" class="mt-4" v-for="monitoredJob in monitoredJobs" :key="monitoredJob.id" :monitoredJob="monitoredJob" />
      <div v-else>
        <p>No monitored jobs found!</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import debounce from 'lodash/debounce'
import MonitoredJob from '@/components/MonitoredJob.vue'
import TagFilter from '@/components/Filters/TagFilter.vue'
import StatusFilter from '@/components/Filters/StatusFilter.vue'

export default {
  props: {
    input: {
      type: Object,
      default: () => {},
    },
    monitoredJobs: {
      type: Array,
      required: true
    }
  },

  components: {
    MonitoredJob,
    TagFilter,
    StatusFilter
  },

  setup(props) {

    const searchTerm = ref(props.input.search)
    const loading = ref(false)

    watch(searchTerm, (term) => {
      loading.value = true;
      handleSearchChange(term);
    })

    const handleSearchChange = debounce((term) => {
      Inertia.get('/monitored-jobs', {
        'search': term
      }, {
        preserveState: true,
        onFinish: () => loading.value = false
      })
    }, 300);

    return {
      searchTerm,
      loading
    }
  }
}
</script>