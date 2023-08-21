import React from 'react';
import { useTranslation } from 'react-i18next';
import { Menu, Dropdown } from 'antd';
import { GlobalOutlined } from '@ant-design/icons';

export default function LanguageSwitch() {
  const { i18n } = useTranslation();

  const languages = [
    { codeShort: 'en', codeLong: 'English' },
    { codeShort: 'lt', codeLong: 'Lithuanian' },
  ];

  const menu = (
    <Menu>
      {languages.map(({ codeShort, codeLong }) => (
        <Menu.Item
          key={`lang${codeShort}`}
          onClick={() => i18n.changeLanguage(codeShort)}
        >
          {codeLong}
        </Menu.Item>
      ))}
    </Menu>
  );

  const selectedLanguage = languages.find(
    ({ codeShort }) => i18n.language === codeShort
  );

  return (
    <Dropdown overlay={menu} trigger={['click']}>
      <a
        className="block w-full ant-dropdown-link"
        onClick={e => e.preventDefault()}
      >
        {selectedLanguage?.codeLong} <GlobalOutlined />
      </a>
    </Dropdown>
  );
}
