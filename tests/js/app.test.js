import test from 'node:test';
import assert from 'node:assert/strict';
import { setWelcomeState } from '../../public/assets/js/app.js';

test('setWelcomeState cập nhật nút chào mừng', () => {
  const button = { textContent: 'Bắt đầu', disabled: false };

  setWelcomeState(button);

  assert.equal(button.textContent, 'Sẵn sàng để xây dựng!');
  assert.equal(button.disabled, true);
});

