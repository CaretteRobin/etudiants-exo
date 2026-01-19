import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import { useCounterStore } from '@/stores/counter'
import CounterComponent from '../CounterComponent.vue'

describe('CounterComponent', () => {
  it('renders the count and reacts to actions', async () => {
    const pinia = createTestingPinia({ stubActions: false, createSpy: vi.fn })
    const wrapper = mount(CounterComponent, {
      global: {
        plugins: [pinia]
      }
    })
    const store = useCounterStore()

    expect(wrapper.find('#counter').text()).toBe('0')

    const buttons = wrapper.findAll('button')
    await buttons[1].trigger('click')

    expect(store.count).toBe(1)
    expect(wrapper.find('#counter').text()).toBe('1')

    await buttons[0].trigger('click')

    expect(store.count).toBe(0)
    expect(wrapper.find('#counter').text()).toBe('0')
  })
})
