import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import InputField from '../InputField.vue'

describe('InputField', () => {
  it('updates the displayed text when typing', async () => {
    const wrapper = mount(InputField)

    const input = wrapper.find('input')
    await input.setValue('Hello Vitest')

    expect(wrapper.find('span').text()).toBe('Hello Vitest')
  })
})
