class CustomWidget extends Widget {
    constructor(configuration) {
        super(configuration, 'customwidget');
        Object.entries(this.datasets).forEach(([id, dataset]) => {
            this.addValueProvider(JSON.stringify({
                dataset: id,
                axis: 'x'
            }), dataset.x.provider);
            this.addValueProvider(JSON.stringify({
                dataset: id,
                axis: 'y'
            }), dataset.y.provider);
            this.addValueProvider(JSON.stringify({
                dataset: id,
                type: 'maxSamples'
            }), dataset.maxSamples.provider);
        });
        console.log('CustomWidget initialized with configuration:', configuration);
    }

    // Override the setValue method to log values to the console
    setValue(attribute, value, time) {
        // super.setValue(attribute, value, time);
        console.log(`Attribute: ${attribute}, Value: ${value}, Time: ${time}`);
    }

    // Override the setArray method to log arrays to the console
    setArray(attribute, array, time) {
        super.setArray(attribute, array, time);
        console.log(`Attribute: ${attribute}, Array: ${array}, Time: ${time}`);
    }

    // Override the setValues method to log multiple values to the console
    setValues(attribute, values, time) {
        super.setValues(attribute, values, time);
        console.log(`Attribute: ${attribute}, Values: ${values}, Time: ${time}`);
    }

    // Override the setArrays method to log multiple arrays to the console
    setArrays(attribute, arrays, time) {
        super.setArrays(attribute, arrays, time);
        console.log(`Attribute: ${attribute}, Arrays: ${arrays}, Time: ${time}`);
    }

    // Custom method to initialize value providers for logging purposes
    initValueProviders() {
        this.valueProviders = this.getValueProviders();
        Object.entries(this.valueProviders).forEach(([attribute, provider]) => {
            this.setValueProvider(attribute, provider.id, provider.target);
            console.log(`ValueProvider initialized for Attribute: ${attribute}, Provider: ${provider}`);
        });
    }

    generateSetters() {
        this.setters = {
            maxSamples: dataset => {
                if (dataset) {
                    if (this.datasets[dataset].maxSamples.function !== null) {
                        this.datasets[dataset].maxSamples.value = this.datasets[dataset].maxSamples.function(this.datasets[dataset].maxSamples.value);
                    }
                }
            },
            offset: dataset => {
                if (dataset) {
                    if (this.datasets[dataset].offset.function !== null) {
                        this.datasets[dataset].offset.value = this.datasets[dataset].offset.function(this.datasets[dataset].offset.value);
                    }
                }
            }
        };
    }
}
