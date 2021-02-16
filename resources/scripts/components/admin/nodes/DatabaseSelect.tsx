import React, { useState } from 'react';
import SearchableSelect, { Option } from '@/components/elements/SearchableSelect';
import searchDatabases from '@/api/admin/databases/searchDatabases';
import { Database } from '@/api/admin/databases/getDatabases';

export default ({ selected }: { selected?: Database | null }) => {
    const [ database, setDatabase ] = useState<Database | null>(selected || null);
    const [ databases, setDatabases ] = useState<Database[]>([]);

    const onSearch = (query: string): Promise<void> => {
        return new Promise((resolve, reject) => {
            searchDatabases({ name: query }).then((databases) => {
                setDatabases(databases);
                return resolve();
            }).catch(reject);
        });
    };

    const onSelect = (database: Database) => {
        setDatabase(database);
    };

    const getSelectedText = (database: Database | null): string => {
        return database?.name || '';
    };

    return (
        <SearchableSelect
            id="database"
            name="Database"
            items={databases}
            selected={database}
            setItems={setDatabases}
            onSearch={onSearch}
            onSelect={onSelect}
            getSelectedText={getSelectedText}
            nullable
        >
            {databases.map(d => (
                <Option key={d.id} id={d.id} item={d} active={d.id === database?.id}>
                    {d.name}
                </Option>
            ))}
        </SearchableSelect>
    );
};
