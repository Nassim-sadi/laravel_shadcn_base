import Alpine from 'alpinejs'
import Swiper from 'swiper'
import 'swiper/css'

window.Alpine = Alpine

const monthNames = {
  en: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
  fr: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
  ar: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
}

const dayNamesMap = {
  en: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
  fr: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
  ar: ['أح', 'إث', 'ثل', 'أر', 'خم', 'جم', 'سب'],
}

Alpine.data('bookingWizard', (timeSlotStyle = 'wheel') => ({
  step: 1,
  timeSlotStyle,
  form: {
    booking_service_id: null,
    date: '',
    start_time: '',
    customer_name: '',
    customer_phone: '',
    notes: '',
  },
  slots: [],
  loadingSlots: false,
  submitting: false,
  success: false,
  successMessage: '',
  error: false,
  errorMessage: '',
  selectedServiceName: '',
  selectedDuration: 0,
  today: new Date(),
  maxDate: new Date(new Date().setDate(new Date().getDate() + 90)),

  // Calendar state
  currentMonth: new Date().getMonth(),
  currentYear: new Date().getFullYear(),
  selectedDate: null,
  panelOpen: false,
  isRtl: document.dir === 'rtl',
  availableDays: {},

  // Swiper instance
  swiperInstance: null,
  swiperReady: false,

  get monthName() {
    const locale = this.isRtl ? 'ar' : (document.documentElement.lang === 'fr' ? 'fr' : 'en')
    return monthNames[locale]?.[this.currentMonth] || monthNames.en[this.currentMonth]
  },

  get dayNames() {
    const locale = this.isRtl ? 'ar' : (document.documentElement.lang === 'fr' ? 'fr' : 'en')
    return dayNamesMap[locale] || dayNamesMap.en
  },

  get daysInMonth() {
    return new Date(this.currentYear, this.currentMonth + 1, 0).getDate()
  },

  get firstDayOffset() {
    return new Date(this.currentYear, this.currentMonth, 1).getDay()
  },

  get selectedDateLabel() {
    if (!this.selectedDate) return ''
    const d = new Date(this.selectedDate)
    const locale = this.isRtl ? 'ar' : (document.documentElement.lang === 'fr' ? 'fr' : 'en')
    return d.toLocaleDateString(locale === 'ar' ? 'ar-SA' : (locale === 'fr' ? 'fr-FR' : 'en-US'), {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    })
  },

  get groupedSlots() {
    const groups = {
      morning: { label: 'Morning (6AM-12PM)', slots: [] },
      afternoon: { label: 'Afternoon (12PM-5PM)', slots: [] },
      evening: { label: 'Evening (5PM-9PM)', slots: [] },
      night: { label: 'Night (9PM-12AM)', slots: [] },
    }

    this.slots.forEach(slot => {
      const hour = parseInt(slot.start_time.split(':')[0])
      if (hour < 12) groups.morning.slots.push(slot)
      else if (hour < 17) groups.afternoon.slots.push(slot)
      else if (hour < 21) groups.evening.slots.push(slot)
      else groups.night.slots.push(slot)
    })

    return Object.values(groups).filter(g => g.slots.length > 0)
  },

  init() {
    const successMsg = document.querySelector('.alert-success')
    if (successMsg) {
      this.success = true
      this.successMessage = successMsg.textContent.trim()
    }
  },

  destroySwiper() {
    if (this.swiperInstance) {
      this.swiperInstance.destroy(true, true)
      this.swiperInstance = null
      this.swiperReady = false
    }
  },

  initSwiper() {
    this.$nextTick(() => {
      this.destroySwiper()
      const container = this.$refs.timePanelSwiper
      if (!container || this.slots.length === 0) return

      this.swiperInstance = new Swiper(container, {
        direction: 'vertical',
        slidesPerView: 5,
        centeredSlides: true,
        spaceBetween: 4,
        grabCursor: true,
        speed: 350,
        on: {
          slideChange: () => {
            const idx = this.swiperInstance?.realIndex ?? -1
            if (idx >= 0 && idx < this.slots.length) {
              this.form.start_time = this.slots[idx].start_time
            }
          },
          click: () => {
            const idx = this.swiperInstance?.clickedIndex ?? -1
            if (idx >= 0 && idx < this.slots.length) {
              this.form.start_time = this.slots[idx].start_time
              this.swiperInstance?.slideTo(idx)
            }
          },
        },
      })
      this.swiperReady = true

      // Auto-select first slot
      if (this.slots.length > 0) {
        const initialIdx = 0
        this.form.start_time = this.slots[initialIdx].start_time
        this.swiperInstance.slideTo(initialIdx, 0)
      }
    })
  },

  selectService(id, name, duration, price) {
    this.form.booking_service_id = id
    this.selectedServiceName = name
    this.selectedDuration = duration
    this.form.date = ''
    this.form.start_time = ''
    this.selectedDate = null
    this.slots = []
    this.availableDays = {}
    this.panelOpen = false
    this.destroySwiper()
  },

  prevMonth() {
    if (this.currentMonth === 0) {
      this.currentMonth = 11
      this.currentYear--
    } else {
      this.currentMonth--
    }
    const now = new Date()
    if (this.currentYear < now.getFullYear() || (this.currentYear === now.getFullYear() && this.currentMonth < now.getMonth())) {
      this.currentMonth = now.getMonth()
      this.currentYear = now.getFullYear()
    }
  },

  nextMonth() {
    if (this.currentMonth === 11) {
      this.currentMonth = 0
      this.currentYear++
    } else {
      this.currentMonth++
    }
    if (this.currentYear > this.maxDate.getFullYear() || (this.currentYear === this.maxDate.getFullYear() && this.currentMonth > this.maxDate.getMonth())) {
      this.currentMonth = this.maxDate.getMonth()
      this.currentYear = this.maxDate.getFullYear()
    }
  },

  isDayDisabled(day) {
    const date = new Date(this.currentYear, this.currentMonth, day)
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    date.setHours(0, 0, 0, 0)
    return date < today || date > this.maxDate
  },

  isToday(day) {
    const today = new Date()
    return day === today.getDate() &&
      this.currentMonth === today.getMonth() &&
      this.currentYear === today.getFullYear()
  },

  isSelected(day) {
    if (!this.selectedDate) return false
    const d = new Date(this.selectedDate)
    return day === d.getDate() &&
      this.currentMonth === d.getMonth() &&
      this.currentYear === d.getFullYear()
  },

  hasAvailableSlots(day) {
    const key = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
    return this.availableDays[key] === true
  },

  getDayClasses(day) {
    const classes = []

    if (this.isDayDisabled(day)) {
      classes.push('text-muted-foreground/40 cursor-not-allowed')
    } else if (this.isSelected(day)) {
      classes.push('bg-primary text-primary-foreground hover:bg-primary/90')
    } else if (this.isToday(day)) {
      classes.push('ring-2 ring-primary text-primary hover:bg-primary/10')
    } else {
      classes.push('hover:bg-base-200 cursor-pointer')
    }

    return classes.join(' ')
  },

  selectDay(day) {
    if (this.isDayDisabled(day)) return

    const date = new Date(this.currentYear, this.currentMonth, day)
    const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`

    if (this.selectedDate && this.selectedDate.getTime() === date.getTime()) {
      this.panelOpen = false
      this.form.date = ''
      this.form.start_time = ''
      this.selectedDate = null
      return
    }

    this.selectedDate = date
    this.form.date = dateStr
    this.form.start_time = ''
    this.panelOpen = true
    this.destroySwiper()
    this.fetchSlots()
  },

  selectTime(time) {
    this.form.start_time = time
    if (this.timeSlotStyle === 'wheel' && this.swiperInstance) {
      const idx = this.slots.findIndex(s => s.start_time === time)
      if (idx >= 0) {
        this.swiperInstance.slideTo(idx)
      }
    }
  },

  async fetchSlots() {
    if (!this.form.date || !this.form.booking_service_id) return

    this.loadingSlots = true
    this.slots = []

    try {
      const response = await fetch(`/bookings/availability/${this.form.booking_service_id}?date=${this.form.date}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      })

      const data = await response.json()
      this.slots = data.data || []

      this.availableDays[this.form.date] = this.slots.length > 0

      this.$nextTick(() => {
        if (this.slots.length > 0 && this.timeSlotStyle === 'wheel') {
          this.initSwiper()
        }
      })
    } catch (err) {
      this.error = true
      this.errorMessage = 'Failed to load available time slots. Please try again.'
    } finally {
      this.loadingSlots = false
    }
  },

  goToStep(newStep) {
    this.step = newStep
    if (newStep !== 2) {
      this.panelOpen = false
      this.destroySwiper()
    }
  },

  async submitBooking() {
    this.submitting = true
    this.error = false

    try {
      const response = await fetch('/bookings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify(this.form),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Failed to submit booking. Please try again.'))
      }

      this.success = true
      this.successMessage = data.message || 'Your booking has been submitted successfully. We will contact you to confirm.'
      this.step = 1
      this.form = {
        booking_service_id: null,
        date: '',
        start_time: '',
        customer_name: '',
        customer_phone: '',
        notes: '',
      }
      this.selectedServiceName = ''
      this.selectedDuration = 0
      this.slots = []
      this.selectedDate = null
      this.panelOpen = false
      this.availableDays = {}
      this.destroySwiper()
    } catch (err) {
      this.error = true
      this.errorMessage = err.message
    } finally {
      this.submitting = false
    }
  },
}))

Alpine.start()
