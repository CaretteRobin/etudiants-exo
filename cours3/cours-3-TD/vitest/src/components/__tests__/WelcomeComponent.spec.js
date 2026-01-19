import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import WelcomeComponent from '../WelcomeComponent.vue'

describe('WelcomeComponent', () => {
  it('renders all welcome sections', () => {
    const wrapper = mount(WelcomeComponent)

    expect(wrapper.text()).toContain('Documentation')
    expect(wrapper.text()).toContain('Tooling')
    expect(wrapper.text()).toContain('Ecosystem')
    expect(wrapper.text()).toContain('Community')
    expect(wrapper.text()).toContain('Support Vue')

    const links = wrapper.findAll('a')
    expect(links.length).toBeGreaterThan(0)
  })
})
