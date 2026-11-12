import {defineConfig} from 'sanity';
import {structureTool} from 'sanity/structure';
import {schemaTypes} from './schema.js';
export default defineConfig({name:'dolce',title:'Dolce Delícias',projectId:'wdisx8zs',dataset:'production',plugins:[structureTool()],schema:{types:schemaTypes}});
