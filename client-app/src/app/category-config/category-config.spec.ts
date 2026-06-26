import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CategoryConfig } from './category-config';

describe('CategoryConfig', () => {
  let component: CategoryConfig;
  let fixture: ComponentFixture<CategoryConfig>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CategoryConfig]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CategoryConfig);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
