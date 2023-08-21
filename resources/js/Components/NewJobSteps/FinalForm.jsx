import React from 'react';
import { Button, List } from 'antd';

export const FinalForm = ({ data }) => {
  const renderListItem = obj => {
    return Object.entries(obj).map(([key, value]) => {
      if (typeof value === 'object' && value !== null) {
        return (
          <List.Item key={key}>
            <strong>{key}:</strong>
            <List
              size="small"
              dataSource={[value]}
              renderItem={item => (
                <List.Item>
                  <FinalForm data={item} />
                </List.Item>
              )}
            />
          </List.Item>
        );
      } else {
        return (
          <List.Item key={key}>
            <strong>{key}:</strong> {JSON.stringify(value)}
          </List.Item>
        );
      }
    });
  };

  return (
    <div className="my-2">
      <List bordered dataSource={[data]} renderItem={renderListItem} />
    </div>
  );
};
