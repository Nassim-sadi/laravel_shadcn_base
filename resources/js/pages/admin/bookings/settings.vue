<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { BasicPage } from '@/components/global-layout'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Switch } from '@/components/ui/switch'
import { Label } from '@/components/ui/label'
import { toast } from 'vue-sonner'
import { useApiFetch } from '@/composables/use-fetch'

const { apiFetch } = useApiFetch()

const timeSlotStyle = ref<'wheel' | 'list'>('wheel')
const allowDuplicatePhone = ref(false)
const saving = ref(false)

async function loadSettings() {
    try {
        const data = await apiFetch('/booking-settings')
        timeSlotStyle.value = (data as any)['booking.time_slot_style'] ?? 'wheel'
        allowDuplicatePhone.value = (data as any)['booking.allow_duplicate_phone'] ?? false
    } catch (error) {
        toast.error('Failed to load booking settings')
    }
}

async function saveSettings() {
    saving.value = true
    try {
        await apiFetch('/booking-settings', {
            method: 'PUT',
            body: {
                time_slot_style: timeSlotStyle.value,
                allow_duplicate_phone: allowDuplicatePhone.value,
            },
        })
        toast.success('Booking settings updated successfully')
    } catch (error: any) {
        const message = error.response?.data?.message || 'Failed to update settings'
        toast.error(message)
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    loadSettings()
})
</script>

<template>
    <BasicPage title="Booking Settings" description="Configure booking module behavior">
        <div class="max-w-2xl space-y-8">
            <div class="space-y-4">
                <h3 class="text-lg font-medium">Time Slot Display Style</h3>
                <p class="text-sm text-muted-foreground">
                    Choose how time slots are displayed on the public booking page.
                </p>

                <div class="space-y-2">
                    <Label>Style</Label>
                    <Select v-model="timeSlotStyle">
                        <SelectTrigger>
                            <SelectValue placeholder="Select style" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="wheel">
                                <div>
                                    <div class="font-medium">Wheel</div>
                                    <div class="text-xs text-muted-foreground">Scrollable wheel with snap effect</div>
                                </div>
                            </SelectItem>
                            <SelectItem value="list">
                                <div>
                                    <div class="font-medium">List</div>
                                    <div class="text-xs text-muted-foreground">Grouped list by time period</div>
                                </div>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-medium">Booking Rules</h3>

                <div class="flex items-center justify-between space-x-2 rounded-lg border p-4">
                    <div class="space-y-0.5">
                        <Label class="text-base">Allow Duplicate Phone Numbers</Label>
                        <p class="text-sm text-muted-foreground">
                            Allow multiple bookings with the same phone number on the same date.
                        </p>
                    </div>
                    <Switch
                        v-model:checked="allowDuplicatePhone"
                        aria-label="Allow duplicate phone numbers"
                    />
                </div>
            </div>

            <div class="flex justify-end">
                <Button @click="saveSettings" :disabled="saving">
                    <span v-if="saving" class="loading loading-spinner loading-sm mr-2"></span>
                    {{ saving ? 'Saving...' : 'Save Settings' }}
                </Button>
            </div>
        </div>
    </BasicPage>
</template>
